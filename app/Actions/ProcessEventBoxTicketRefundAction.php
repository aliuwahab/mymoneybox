<?php

namespace App\Actions;

use App\Enums\RefundStatus;
use App\Models\EventBoxTicketRefund;
use App\Payment\PaymentManager;
use App\Payment\Providers\TrendiPayProvider;
use Illuminate\Support\Facades\Log;

class ProcessEventBoxTicketRefundAction
{
    public function execute(EventBoxTicketRefund $refund): array
    {
        if ($refund->status !== RefundStatus::Pending) {
            return ['success' => false, 'message' => 'Refund is not pending.'];
        }

        if (! $refund->recipient_account_number || ! $refund->recipient_network) {
            $refund->update([
                'status' => RefundStatus::Failed,
                'failed_at' => now(),
                'failure_reason' => 'Missing payer account number or mobile money network.',
            ]);

            return ['success' => false, 'message' => 'Missing refund destination.'];
        }

        if ((float) $refund->refund_amount <= 0) {
            $refund->update([
                'status' => RefundStatus::Failed,
                'failed_at' => now(),
                'failure_reason' => 'Refund amount is zero after charges.',
            ]);

            return ['success' => false, 'message' => 'Refund amount is zero after charges.'];
        }

        try {
            $provider = app(PaymentManager::class)->provider($refund->payment_provider ?? 'trendipay');

            if ($provider instanceof TrendiPayProvider) {
                $balance = $provider->getBalance();

                if (! ($balance['success'] ?? false)) {
                    return ['success' => false, 'message' => 'Could not verify available balance.'];
                }

                if (($balance['available_balance'] ?? 0) < (float) $refund->refund_amount) {
                    return ['success' => false, 'message' => 'Insufficient provider balance.'];
                }
            }

            $transferReference = $refund->reference.'-REFUND-'.now()->timestamp;

            $result = $provider->transferAmount([
                'reference' => $transferReference,
                'amount' => $refund->refund_amount,
                'account_number' => $refund->recipient_account_number,
                'account_name' => $refund->recipient_name ?? '',
                'network' => $refund->recipient_network,
                'sender_name' => config('app.name'),
                'description' => "Ticket refund: {$refund->reference}",
            ]);

            if ($result['success'] ?? false) {
                $refund->update([
                    'status' => RefundStatus::Processing,
                    'transaction_reference' => $result['transaction_reference'] ?? $transferReference,
                    'processed_at' => now(),
                    'payment_metadata' => array_merge($refund->payment_metadata ?? [], [
                        'transfer_reference' => $transferReference,
                        'disbursement' => $result,
                    ]),
                ]);

                activity('eventbox')
                    ->performedOn($refund->ticket->eventBox)
                    ->event('ticket_refund_processing')
                    ->withProperties([
                        'ticket_id' => $refund->event_box_ticket_id,
                        'refund_id' => $refund->id,
                        'refund_reference' => $refund->reference,
                        'refund_amount' => (float) $refund->refund_amount,
                    ])
                    ->log('Ticket refund submitted to payment provider');

                return ['success' => true];
            }

            $refund->update([
                'payment_metadata' => array_merge($refund->payment_metadata ?? [], [
                    'attempt' => $result,
                    'attempted_at' => now()->toDateTimeString(),
                ]),
            ]);

            return ['success' => false, 'message' => $result['message'] ?? 'Refund transfer failed.'];
        } catch (\Throwable $e) {
            Log::error('EventBox refund processing failed', [
                'refund_id' => $refund->id,
                'reference' => $refund->reference,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Refund service unavailable.'];
        }
    }
}
