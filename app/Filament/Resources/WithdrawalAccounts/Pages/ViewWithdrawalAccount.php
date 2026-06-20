<?php

namespace App\Filament\Resources\WithdrawalAccounts\Pages;

use App\Filament\Resources\WithdrawalAccounts\WithdrawalAccountResource;
use App\Models\WithdrawalAccount;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewWithdrawalAccount extends ViewRecord
{
    protected static string $resource = WithdrawalAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Verify Account')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_verified' => true]);
                    $this->refreshFormData(['is_verified']);
                    Notification::make()->success()->title('Account verified')->send();
                })
                ->visible(fn () => ! $this->record->is_verified),

            Action::make('unverify')
                ->label('Remove Verification')
                ->icon('heroicon-o-x-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_verified' => false]);
                    $this->refreshFormData(['is_verified']);
                    Notification::make()->warning()->title('Verification removed')->send();
                })
                ->visible(fn () => $this->record->is_verified && ! $this->record->hasDisbursements()),
        ];
    }
}