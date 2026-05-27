<?php

namespace App\Filament\Resources\EventBoxRefunds\Pages;

use App\Actions\ProcessEventBoxTicketRefundAction;
use App\Enums\RefundStatus;
use App\Filament\Resources\EventBoxRefunds\EventBoxTicketRefundResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEventBoxTicketRefund extends ViewRecord
{
    protected static string $resource = EventBoxTicketRefundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label('Retry Refund')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Retry Refund')
                ->modalDescription('This will re-submit the refund to the payment provider. Verify the recipient details are still correct before retrying.')
                ->action(function (ProcessEventBoxTicketRefundAction $processAction) {
                    $this->record->update([
                        'status'         => RefundStatus::Pending,
                        'failed_at'      => null,
                        'failure_reason' => null,
                    ]);

                    $result = $processAction->execute($this->record->fresh());

                    $this->refreshFormData(['status', 'processed_at', 'failure_reason', 'transaction_reference', 'payment_metadata']);

                    if ($result['success']) {
                        Notification::make()->success()->title('Refund re-submitted to provider')->send();
                    } else {
                        Notification::make()->warning()->title($result['message'] ?? 'Retry failed')->send();
                    }
                })
                ->visible(fn () => $this->record->status === RefundStatus::Failed),
        ];
    }
}