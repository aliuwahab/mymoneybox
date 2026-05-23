<?php

namespace App\Filament\Resources\MoneyBoxWithdrawals\Pages;

use App\Actions\DisburseWithdrawalAction;
use App\Enums\WithdrawalStatus;
use App\Filament\Resources\MoneyBoxWithdrawals\MoneyBoxWithdrawalResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewMoneyBoxWithdrawal extends ViewRecord
{
    protected static string $resource = MoneyBoxWithdrawalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addNote')
                ->label('Add Comment')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->form([
                    Textarea::make('note')
                        ->label('Comment')
                        ->required()
                        ->rows(3),
                ])
                ->action(function (array $data) {
                    $this->record->notes()->create([
                        'user_id' => auth()->id(),
                        'note' => $data['note'],
                        'is_admin' => true,
                    ]);
                    Notification::make()->success()->title('Comment added')->send();
                }),

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
                    event(new \App\Events\WithdrawalApproved($this->record));
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

            Action::make('voidAndDelete')
                ->label('Void & Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Void & Delete Withdrawal')
                ->modalDescription('This permanently deletes the withdrawal record and restores the available balance. Use only to correct erroneous disbursed records.')
                ->action(function () {
                    $this->record->delete();
                    Notification::make()->success()->title('Withdrawal deleted — balance restored')->send();
                    $this->redirect(MoneyBoxWithdrawalResource::getUrl('index'));
                })
                ->visible(fn () => auth()->user()?->email === 'aliuwahab@gmail.com'),

            Action::make('disburse')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Send Funds to User')
                ->modalDescription('This will submit the transfer to the payment provider. The status will update to Disbursed once the payment provider confirms.')
                ->action(function (DisburseWithdrawalAction $action) {
                    $result = $action->execute($this->record);
                    $this->refreshFormData(['status', 'transaction_reference']);

                    if ($result['success']) {
                        Notification::make()->success()->title('Transfer submitted')->body('Awaiting payment provider confirmation.')->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Transfer failed')
                            ->body($result['message'] ?? 'Please try again.')
                            ->persistent()
                            ->send();
                    }
                })
                ->visible(fn () => $this->record->canBeDisbursed()),
        ];
    }
}
