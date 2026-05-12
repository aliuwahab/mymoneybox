<?php

namespace App\Filament\Resources\PiggyBoxWithdrawals\Pages;

use App\Enums\WithdrawalStatus;
use App\Filament\Resources\PiggyBoxWithdrawals\PiggyBoxWithdrawalResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPiggyBoxWithdrawal extends ViewRecord
{
    protected static string $resource = PiggyBoxWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status'       => WithdrawalStatus::Approved,
                        'processed_by' => auth()->id(),
                        'processed_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'processed_by', 'processed_at']);
                    Notification::make()->success()->title('Withdrawal approved')->send();
                })
                ->visible(fn () => $this->record->canBeApproved()),

            Action::make('reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Reason for rejection')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status'           => WithdrawalStatus::Rejected,
                        'rejection_reason' => $data['rejection_reason'],
                        'processed_by'     => auth()->id(),
                        'processed_at'     => now(),
                    ]);
                    $this->refreshFormData(['status', 'rejection_reason', 'processed_by', 'processed_at']);
                    Notification::make()->warning()->title('Withdrawal rejected')->send();
                })
                ->visible(fn () => $this->record->canBeRejected()),

            Action::make('disburse')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Mark as Disbursed')
                ->modalDescription('Confirm that funds have been sent to the user\'s account.')
                ->action(function () {
                    $this->record->update([
                        'status'       => WithdrawalStatus::Disbursed,
                        'disbursed_at' => now(),
                    ]);
                    $this->refreshFormData(['status', 'disbursed_at']);
                    Notification::make()->success()->title('Marked as disbursed')->send();
                })
                ->visible(fn () => $this->record->canBeDisbursed()),
        ];
    }
}