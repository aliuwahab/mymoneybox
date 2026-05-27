<?php

namespace App\Http\Controllers;

use App\Actions\UpdateMoneyBoxStatsAction;
use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Contribution;
use App\Models\EventBoxTicketRefund;
use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrendiPayWebhookController extends Controller
{
    public function __construct(
        protected TrendiPayProvider $trendiPayProvider,
        protected UpdateMoneyBoxStatsAction $updateStatsAction
    ) {}

    /**
     * Handle TrendiPay webhook notification (server-to-server)
     *
     * This receives payment status updates from TrendiPay after payment
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $reference = $this->extractWebhookReference($payload);
        $rawBodyHash = hash('sha256', $request->getContent());

        Log::info('TrendiPay webhook received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'reference' => $reference,
        ]);

        // Validate webhook payload structure
        $validationFailedResponse = $this->validateWebhookPayload($payload);
        if ($validationFailedResponse) {
            return $validationFailedResponse;
        }

        try {
            // Process the webhook payload through TrendiPay provider
            $webhookData = $this->trendiPayProvider->handleWebhook($payload);
            $reference = $webhookData['reference'];

            // Disbursement callbacks have the suffix -DISBURSE-{timestamp}
            if (str_contains($reference, '-DISBURSE-')) {
                return $this->handleDisbursementCallback($webhookData, $reference);
            }

            // EventBox ticket purchases have the prefix EVT-
            if (str_starts_with($reference, 'EVT-')) {
                return $this->handleEventTicketCallback($webhookData, $reference);
            }

            if (str_starts_with($reference, 'ERF-')) {
                return $this->handleEventTicketRefundCallback($webhookData, $reference);
            }

            // Find the contribution
            $contribution = Contribution::query()->where('payment_reference', $reference)->first();

            if (! $contribution) {
                Log::warning('TrendiPay webhook: Contribution not found', [
                    'reference' => $reference,
                ]);

                return response()->json(['status' => 'error', 'message' => 'Contribution not found'], 404);
            }

            $this->auditContributionWebhook($reference, $webhookData['status'], $rawBodyHash);

            // Idempotency: terminal states should not be reprocessed
            if (in_array($contribution->payment_status, [PaymentStatus::Completed, PaymentStatus::Failed])) {
                Log::info('TrendiPay webhook: already processed, skipping', [
                    'reference' => $reference,
                    'current_status' => $contribution->payment_status->value,
                ]);

                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            $webhookData = $this->withVerifiedCollectionTransaction($webhookData);

            if (($webhookData['provider_verified'] ?? true) === false) {
                $contribution->update([
                    'transaction_rrn' => $webhookData['transaction_rrn'] ?? $contribution->transaction_rrn,
                    'payment_metadata' => $webhookData['raw_data'] ?? null,
                ]);

                return response()->json(['status' => 'pending', 'message' => 'Payment verification pending'], 202);
            }

            $paymentStatus = match ($webhookData['status']) {
                'completed' => PaymentStatus::Completed,
                'failed' => PaymentStatus::Failed,
                default => PaymentStatus::Pending,
            };

            $paymentMetadata = $webhookData['raw_data'] ?? [];
            $amountMatches = $contribution->matchesPaidAmount($webhookData['amount'] ?? null);

            if ($paymentStatus === PaymentStatus::Completed && ! $amountMatches) {
                Log::warning('TrendiPay webhook: contribution amount mismatch', [
                    'contribution_id' => $contribution->id,
                    'reference' => $reference,
                    'expected_amount' => (float) $contribution->amount,
                    'verified_amount' => (float) $webhookData['amount'],
                ]);

                $paymentStatus = PaymentStatus::Failed;
                $paymentMetadata['amount_mismatch'] = true;
            }

            $contribution->update([
                'payment_status' => $paymentStatus,
                'transaction_rrn' => $webhookData['transaction_rrn'] ?? null,
                'payment_metadata' => $paymentMetadata,
            ]);

            Log::info('TrendiPay webhook: status updated', [
                'contribution_id' => $contribution->id,
                'reference' => $reference,
                'status' => $paymentStatus->value,
            ]);

            if ($paymentStatus === PaymentStatus::Completed) {
                $this->updateStatsAction->execute($contribution->moneyBox, $contribution);
                event(new \App\Events\ContributionProcessed($contribution, $contribution->moneyBox));
            }

            return response()->json(['status' => 'success', 'message' => 'Payment processed successfully'], 200);

        } catch (\Exception $e) {
            Log::error('TrendiPay webhook: Processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal error processing webhook',
            ], 500);
        }
    }

    private function extractWebhookReference(array $payload): ?string
    {
        $reference = data_get($payload, 'data.reference');

        return is_string($reference) ? $reference : null;
    }

    private function auditContributionWebhook(?string $reference, ?string $status, string $eventHash): void
    {
        if (! $reference || str_starts_with($reference, 'EVT-') || str_starts_with($reference, 'ERF-') || str_contains($reference, '-DISBURSE-')) {
            return;
        }

        $contribution = Contribution::query()
            ->where('payment_reference', $reference)
            ->first();

        if (! $contribution) {
            return;
        }

        $contribution->increment('webhook_attempts');
        $contribution->forceFill([
            'webhook_last_received_at' => now(),
            'webhook_last_status' => $status,
            'webhook_last_event_hash' => $eventHash,
        ])->save();
    }

    private function withVerifiedCollectionTransaction(array $webhookData): array
    {
        if (! in_array($webhookData['status'] ?? null, ['completed', 'failed'], true)) {
            return $webhookData + ['provider_verified' => null];
        }

        $verification = $this->trendiPayProvider->verifyCollectionTransaction(
            (string) ($webhookData['transaction_rrn'] ?? ''),
            $webhookData['reference'] ?? null,
        );

        $rawData = array_merge($webhookData['raw_data'] ?? [], [
            'provider_verification' => $verification['raw_data'] ?? $verification,
            'provider_verified' => $verification['verified'] ?? false,
            'provider_verified_at' => now()->toDateTimeString(),
        ]);

        if (($verification['verified'] ?? false) !== true) {
            Log::warning('TrendiPay webhook: provider verification failed', [
                'reference' => $webhookData['reference'] ?? null,
                'rrn' => $webhookData['transaction_rrn'] ?? null,
                'message' => $verification['message'] ?? null,
            ]);

            return array_merge($webhookData, [
                'success' => false,
                'status' => 'pending',
                'provider_verified' => false,
                'provider_verification' => $verification,
                'raw_data' => $rawData,
            ]);
        }

        return array_merge($webhookData, [
            'success' => $verification['success'] ?? false,
            'status' => $verification['status'] ?? 'pending',
            'amount' => $verification['amount'] ?? $webhookData['amount'] ?? 0,
            'reference' => $verification['reference'] ?? $webhookData['reference'] ?? null,
            'transaction_rrn' => $verification['transaction_rrn'] ?? $webhookData['transaction_rrn'] ?? null,
            'transaction_id' => $verification['transaction_id'] ?? $webhookData['transaction_id'] ?? null,
            'external_id' => $verification['external_id'] ?? $webhookData['external_id'] ?? null,
            'account_number' => $verification['account_number'] ?? $webhookData['account_number'] ?? null,
            'payment_method' => $verification['payment_method'] ?? $webhookData['payment_method'] ?? null,
            'response_code' => $verification['response_code'] ?? $webhookData['response_code'] ?? null,
            'reason' => $verification['reason'] ?? $webhookData['reason'] ?? null,
            'provider_verified' => true,
            'provider_verification' => $verification,
            'raw_data' => $rawData,
        ]);
    }

    /**
     * Handle a disbursement callback from TrendiPay.
     * The disbursement reference is {withdrawal_reference}-DISBURSE-{timestamp}.
     */
    protected function handleDisbursementCallback(array $webhookData, string $disbursementReference): JsonResponse
    {
        // Strip the -DISBURSE-{timestamp} suffix to recover the original withdrawal reference
        $withdrawalReference = preg_replace('/-DISBURSE-\d+$/', '', $disbursementReference);

        Log::info('TrendiPay disbursement webhook received', [
            'disbursement_reference' => $disbursementReference,
            'withdrawal_reference' => $withdrawalReference,
            'status' => $webhookData['status'],
        ]);

        // Try MoneyBox withdrawal first, then PiggyBox
        $withdrawal = MoneyBoxWithdrawal::where('reference', $withdrawalReference)->first()
            ?? PiggyBoxWithdrawal::where('reference', $withdrawalReference)->first();

        if (! $withdrawal) {
            Log::warning('TrendiPay disbursement webhook: withdrawal not found', [
                'withdrawal_reference' => $withdrawalReference,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Withdrawal not found'], 404);
        }

        $isSuccess = $webhookData['status'] === 'completed';

        if ($isSuccess) {
            $withdrawal->update([
                'status' => WithdrawalStatus::Disbursed,
                'disbursed_at' => now(),
                'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                    'disbursement_confirmed' => true,
                    'disbursement_callback' => $webhookData['raw_data'] ?? [],
                    'confirmed_at' => now()->toDateTimeString(),
                ]),
            ]);

            Log::info('TrendiPay disbursement confirmed', ['reference' => $withdrawalReference]);
            event(new \App\Events\WithdrawalDisbursed($withdrawal));
        } else {
            // Transfer ultimately failed — mark accordingly
            $withdrawal->update([
                'status' => WithdrawalStatus::Failed,
                'failure_reason' => $webhookData['reason'] ?? 'Disbursement failed via TrendiPay callback',
                'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                    'disbursement_failed_callback' => $webhookData['raw_data'] ?? [],
                    'failed_at' => now()->toDateTimeString(),
                ]),
            ]);

            Log::warning('TrendiPay disbursement failed via callback', [
                'reference' => $withdrawalReference,
                'reason' => $webhookData['reason'] ?? null,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Disbursement callback processed'], 200);
    }

    /**
     * Handle an EventBox ticket payment callback.
     */
    /**
     * Handle an EventBox ticket payment callback.
     * Supports single-ticket (payment_reference) and multi-ticket group (payment_group) purchases.
     */
    private function handleEventTicketCallback(array $webhookData, string $reference): JsonResponse
    {
        // Find all tickets for this payment — single ticket or whole group
        $tickets = \App\Models\EventBoxTicket::where('payment_reference', $reference)->get();

        if ($tickets->isEmpty()) {
            $tickets = \App\Models\EventBoxTicket::where('payment_group', $reference)->get();
        }

        if ($tickets->isEmpty()) {
            Log::warning('EventBox ticket webhook: ticket(s) not found', ['reference' => $reference]);

            return response()->json(['status' => 'error', 'message' => 'Ticket not found'], 404);
        }

        // Idempotency: all tickets already completed
        if ($tickets->every(fn ($t) => $t->payment_status === \App\Enums\PaymentStatus::Completed)) {
            return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
        }

        $paymentStatus = match ($webhookData['status']) {
            'completed' => \App\Enums\PaymentStatus::Completed,
            'failed' => \App\Enums\PaymentStatus::Failed,
            default => \App\Enums\PaymentStatus::Pending,
        };

        if (in_array($paymentStatus, [PaymentStatus::Completed, PaymentStatus::Failed], true)) {
            $webhookData = $this->withVerifiedCollectionTransaction($webhookData);

            if (($webhookData['provider_verified'] ?? true) === false) {
                return response()->json(['status' => 'pending', 'message' => 'Payment verification pending'], 202);
            }

            $paymentStatus = match ($webhookData['status']) {
                'completed' => \App\Enums\PaymentStatus::Completed,
                'failed' => \App\Enums\PaymentStatus::Failed,
                default => \App\Enums\PaymentStatus::Pending,
            };
        }

        $expectedAmount = (float) $tickets->sum('amount');
        if ($paymentStatus === PaymentStatus::Completed && isset($webhookData['amount']) && (float) $webhookData['amount'] > 0 && (int) round((float) $webhookData['amount'] * 100) !== (int) round($expectedAmount * 100)) {
            Log::warning('EventBox ticket webhook: amount mismatch', [
                'reference' => $reference,
                'expected_amount' => $expectedAmount,
                'verified_amount' => (float) $webhookData['amount'],
            ]);

            $paymentStatus = PaymentStatus::Failed;
            $webhookData['raw_data']['amount_mismatch'] = true;
        }

        foreach ($tickets as $ticket) {
            $updates = [
                'payment_status' => $paymentStatus,
                'payment_method' => $webhookData['payment_method'] ?? $ticket->payment_method,
                'payment_account_number' => $webhookData['account_number'] ?? $ticket->payment_account_number,
                'transaction_rrn' => $webhookData['transaction_rrn'] ?? $ticket->transaction_rrn,
                'payment_metadata' => $webhookData['raw_data'] ?? null,
            ];

            if ($paymentStatus === \App\Enums\PaymentStatus::Completed) {
                $updates['code'] = \App\Models\EventBoxTicket::generateCode();
                $updates['status'] = 'unused';
            }

            // Atomic: skip if another concurrent webhook call already completed this ticket
            $affected = \App\Models\EventBoxTicket::where('id', $ticket->id)
                ->where('payment_status', '!=', \App\Enums\PaymentStatus::Completed)
                ->update($updates);

            if (! $affected) {
                continue;
            }

            if ($paymentStatus === \App\Enums\PaymentStatus::Completed) {
                $ticket->eventBox->increment('tickets_sold');

                if ($ticket->ticket_type_id) {
                    \App\Models\EventBoxTicketType::where('id', $ticket->ticket_type_id)->increment('sold');
                }

                event(new \App\Events\TicketIssued($ticket->fresh()));
            }
        }

        // Re-check sold-out after all tickets in the group are processed
        if ($paymentStatus === \App\Enums\PaymentStatus::Completed) {
            $eventBox = $tickets->first()->eventBox->fresh()->load('ticketTypes');
            $overallFull = $eventBox->capacity && $eventBox->tickets_sold >= $eventBox->capacity;
            $typesFull = $eventBox->ticketTypes->isNotEmpty()
                && $eventBox->ticketTypes->every(fn ($t) => ! $t->isAvailable());

            if ($overallFull || $typesFull) {
                $eventBox->update(['status' => 'sold_out']);
            }
        }

        Log::info('EventBox ticket webhook processed', [
            'reference' => $reference,
            'count' => $tickets->count(),
            'status' => $paymentStatus->value,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Ticket(s) processed'], 200);
    }

    private function handleEventTicketRefundCallback(array $webhookData, string $reference): JsonResponse
    {
        $refundReference = preg_replace('/-REFUND-\d+$/', '', $reference);

        $refund = EventBoxTicketRefund::with('ticket.eventBox')
            ->where('reference', $refundReference)
            ->first();

        if (! $refund) {
            Log::warning('EventBox ticket refund webhook: refund not found', ['reference' => $reference]);

            return response()->json(['status' => 'error', 'message' => 'Refund not found'], 404);
        }

        if (in_array($refund->status, [RefundStatus::Completed, RefundStatus::Failed], true)) {
            return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
        }

        if ($webhookData['status'] === 'completed') {
            $refund->update([
                'status' => RefundStatus::Completed,
                'completed_at' => now(),
                'transaction_reference' => $webhookData['external_id'] ?? $webhookData['transaction_id'] ?? $refund->transaction_reference,
                'payment_metadata' => array_merge($refund->payment_metadata ?? [], [
                    'callback' => $webhookData['raw_data'] ?? [],
                    'callback_at' => now()->toDateTimeString(),
                ]),
            ]);

            activity('eventbox')
                ->performedOn($refund->ticket->eventBox)
                ->event('ticket_refund_completed')
                ->withProperties([
                    'ticket_id' => $refund->event_box_ticket_id,
                    'refund_id' => $refund->id,
                    'refund_reference' => $refund->reference,
                    'refund_amount' => (float) $refund->refund_amount,
                ])
                ->log('Ticket refund completed');
        } elseif ($webhookData['status'] === 'failed') {
            $refund->update([
                'status' => RefundStatus::Failed,
                'failed_at' => now(),
                'failure_reason' => $webhookData['reason'] ?? 'Refund failed via payment provider callback.',
                'payment_metadata' => array_merge($refund->payment_metadata ?? [], [
                    'failed_callback' => $webhookData['raw_data'] ?? [],
                    'failed_at' => now()->toDateTimeString(),
                ]),
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Refund callback processed'], 200);
    }

    /**
     * Validate webhook payload structure
     *
     * @return JsonResponse|null Returns error response if invalid, null if valid
     */
    protected function validateWebhookPayload(array $payload): ?JsonResponse
    {
        if (! isset($payload['data'])) {
            Log::warning('TrendiPay webhook: Invalid payload - missing data field', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing data field',
            ], 400);
        }

        $data = $payload['data'];

        // Check for required fields
        if (! isset($data['reference'])) {
            Log::warning('TrendiPay webhook: Invalid payload - missing reference', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing reference',
            ], 400);
        }

        if (! isset($data['status'])) {
            Log::warning('TrendiPay webhook: Invalid payload - missing status', [
                'payload' => $payload,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing status',
            ], 400);
        }

        return null; // Validation passed
    }
}
