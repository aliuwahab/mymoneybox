<?php

namespace App\Http\Controllers;

use App\Actions\UpdateMoneyBoxStatsAction;
use App\Enums\PaymentStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Contribution;
use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use App\Models\PiggyDonation;
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
        Log::info('TrendiPay webhook received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $payload = $request->all();

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

            // Find the contribution
            $contribution = Contribution::query()->where('payment_reference', $reference)->first();

            if (!$contribution) {
                Log::warning('TrendiPay webhook: Contribution not found', [
                    'reference' => $reference,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Contribution not found'], 404);
            }

            // Idempotency: terminal states should not be reprocessed
            if (in_array($contribution->payment_status, [PaymentStatus::Completed, PaymentStatus::Failed])) {
                Log::info('TrendiPay webhook: already processed, skipping', [
                    'reference' => $reference,
                    'current_status' => $contribution->payment_status->value,
                ]);
                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            $paymentStatus = match($webhookData['status']) {
                'completed' => PaymentStatus::Completed,
                'failed'    => PaymentStatus::Failed,
                default     => PaymentStatus::Pending,
            };

            $contribution->update([
                'payment_status'   => $paymentStatus,
                'transaction_rrn'  => $webhookData['transaction_rrn'] ?? null,
                'payment_metadata' => $webhookData['raw_data'] ?? null,
            ]);

            Log::info('TrendiPay webhook: status updated', [
                'contribution_id' => $contribution->id,
                'reference'       => $reference,
                'status'          => $paymentStatus->value,
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
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal error processing webhook'
            ], 500);
        }
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
            'withdrawal_reference'   => $withdrawalReference,
            'status'                 => $webhookData['status'],
        ]);

        // Try MoneyBox withdrawal first, then PiggyBox
        $withdrawal = MoneyBoxWithdrawal::where('reference', $withdrawalReference)->first()
            ?? PiggyBoxWithdrawal::where('reference', $withdrawalReference)->first();

        if (!$withdrawal) {
            Log::warning('TrendiPay disbursement webhook: withdrawal not found', [
                'withdrawal_reference' => $withdrawalReference,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Withdrawal not found'], 404);
        }

        $isSuccess = $webhookData['status'] === 'completed';

        if ($isSuccess) {
            $withdrawal->update([
                'status'           => WithdrawalStatus::Disbursed,
                'disbursed_at'     => now(),
                'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                    'disbursement_confirmed' => true,
                    'disbursement_callback'  => $webhookData['raw_data'] ?? [],
                    'confirmed_at'           => now()->toDateTimeString(),
                ]),
            ]);

            Log::info('TrendiPay disbursement confirmed', ['reference' => $withdrawalReference]);
            event(new \App\Events\WithdrawalDisbursed($withdrawal));
        } else {
            // Transfer ultimately failed — mark accordingly
            $withdrawal->update([
                'status'         => WithdrawalStatus::Failed,
                'failure_reason' => $webhookData['reason'] ?? 'Disbursement failed via TrendiPay callback',
                'payment_metadata' => array_merge($withdrawal->payment_metadata ?? [], [
                    'disbursement_failed_callback' => $webhookData['raw_data'] ?? [],
                    'failed_at'                    => now()->toDateTimeString(),
                ]),
            ]);

            Log::warning('TrendiPay disbursement failed via callback', [
                'reference' => $withdrawalReference,
                'reason'    => $webhookData['reason'] ?? null,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Disbursement callback processed'], 200);
    }

    /**
     * Handle an EventBox ticket payment callback.
     */
    private function handleEventTicketCallback(array $webhookData, string $reference): JsonResponse
    {
        $ticket = \App\Models\EventBoxTicket::where('payment_reference', $reference)->first();

        if (!$ticket) {
            Log::warning('EventBox ticket webhook: ticket not found', ['reference' => $reference]);
            return response()->json(['status' => 'error', 'message' => 'Ticket not found'], 404);
        }

        // Idempotency
        if ($ticket->payment_status === \App\Enums\PaymentStatus::Completed) {
            return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
        }

        $paymentStatus = match($webhookData['status']) {
            'completed' => \App\Enums\PaymentStatus::Completed,
            'failed'    => \App\Enums\PaymentStatus::Failed,
            default     => \App\Enums\PaymentStatus::Pending,
        };

        $updates = [
            'payment_status'   => $paymentStatus,
            'payment_metadata' => $webhookData['raw_data'] ?? null,
        ];

        if ($paymentStatus === \App\Enums\PaymentStatus::Completed) {
            // Generate unique ticket code
            $code = \App\Models\EventBoxTicket::generateCode();
            $updates['code']   = $code;
            $updates['status'] = 'unused';

            // Increment tickets_sold on the event
            $ticket->eventBox->increment('tickets_sold');

            // Mark sold out if capacity reached
            $eventBox = $ticket->eventBox->fresh();
            if ($eventBox->capacity && $eventBox->tickets_sold >= $eventBox->capacity) {
                $eventBox->update(['status' => 'sold_out']);
            }
        }

        $ticket->update($updates);

        if ($paymentStatus === \App\Enums\PaymentStatus::Completed) {
            event(new \App\Events\TicketIssued($ticket->fresh()));
        }

        Log::info('EventBox ticket webhook processed', [
            'reference' => $reference,
            'status'    => $paymentStatus->value,
            'code'      => $updates['code'] ?? null,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Ticket processed'], 200);
    }

    /**
     * Validate webhook payload structure
     *
     * @return JsonResponse|null Returns error response if invalid, null if valid
     */
    protected function validateWebhookPayload(array $payload): ?JsonResponse
    {
        if (!isset($payload['data'])) {
            Log::warning('TrendiPay webhook: Invalid payload - missing data field', [
                'payload' => $payload
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing data field'
            ], 400);
        }

        $data = $payload['data'];

        // Check for required fields
        if (!isset($data['reference'])) {
            Log::warning('TrendiPay webhook: Invalid payload - missing reference', [
                'payload' => $payload
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing reference'
            ], 400);
        }

        if (!isset($data['status'])) {
            Log::warning('TrendiPay webhook: Invalid payload - missing status', [
                'payload' => $payload
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook payload: missing status'
            ], 400);
        }

        return null; // Validation passed
    }
}
