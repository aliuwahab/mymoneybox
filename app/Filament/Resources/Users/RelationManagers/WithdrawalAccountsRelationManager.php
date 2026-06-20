<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\AccountType;
use App\Models\WithdrawalAccount;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WithdrawalAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'withdrawalAccounts';

    protected static ?string $title = 'Withdrawal Accounts';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('account_type')
                    ->badge()
                    ->color(fn (AccountType $state) => match ($state) {
                        AccountType::MobileMoney => 'success',
                        AccountType::Bank        => 'info',
                    })
                    ->formatStateUsing(fn (AccountType $state) => $state->value === 'mobile_money' ? 'Mobile Money' : 'Bank'),
                TextColumn::make('account_name'),
                TextColumn::make('account_number')
                    ->label('Number')
                    ->copyable()
                    ->formatStateUsing(fn (WithdrawalAccount $record) => $record->account_number),
                TextColumn::make('mobile_network')
                    ->label('Network / Bank')
                    ->formatStateUsing(fn ($state, WithdrawalAccount $record) => $record->account_type === AccountType::MobileMoney
                        ? strtoupper($record->mobile_network?->value ?? '—')
                        : ($record->bank_name ?? '—')
                    ),
                IconColumn::make('is_verified')->label('Verified')->boolean(),
                IconColumn::make('is_active')->label('Active')->boolean(),
                IconColumn::make('is_default')->label('Default')->boolean(),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (WithdrawalAccount $record) {
                        $record->update(['is_verified' => true]);
                        Notification::make()->success()->title('Account verified')->send();
                    })
                    ->visible(fn (WithdrawalAccount $record) => ! $record->is_verified),
                Action::make('unverify')
                    ->label('Unverify')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (WithdrawalAccount $record) {
                        $record->update(['is_verified' => false]);
                        Notification::make()->warning()->title('Verification removed')->send();
                    })
                    ->visible(fn (WithdrawalAccount $record) => $record->is_verified && ! $record->hasDisbursements()),
            ]);
    }
}