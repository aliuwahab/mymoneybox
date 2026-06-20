<?php

namespace App\Filament\Resources\WithdrawalAccounts\Tables;

use App\Enums\AccountType;
use App\Models\WithdrawalAccount;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WithdrawalAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('account_type')
                    ->badge()
                    ->color(fn (AccountType $state) => match ($state) {
                        AccountType::MobileMoney => 'success',
                        AccountType::Bank        => 'info',
                    })
                    ->formatStateUsing(fn (AccountType $state) => $state->value === 'mobile_money' ? 'Mobile Money' : 'Bank'),
                TextColumn::make('account_name')
                    ->searchable(),
                TextColumn::make('account_number')
                    ->label('Number')
                    ->searchable()
                    ->copyable()
                    ->formatStateUsing(fn (WithdrawalAccount $record) => $record->maskAccountNumber()),
                TextColumn::make('mobile_network')
                    ->label('Network / Bank')
                    ->formatStateUsing(fn ($state, WithdrawalAccount $record) => $record->account_type === AccountType::MobileMoney
                        ? strtoupper($record->mobile_network?->value ?? '—')
                        : ($record->bank_name ?? '—')
                    ),
                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Added')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('account_type')
                    ->options([
                        'mobile_money' => 'Mobile Money',
                        'bank'         => 'Bank',
                    ]),
                SelectFilter::make('is_verified')
                    ->label('Verification')
                    ->options([
                        '1' => 'Verified',
                        '0' => 'Unverified',
                    ]),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verify Withdrawal Account')
                    ->modalDescription('Mark this account as verified. It will become eligible for disbursements.')
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
                    ->modalHeading('Remove Verification')
                    ->modalDescription('This account will no longer be eligible for disbursements until re-verified.')
                    ->action(function (WithdrawalAccount $record) {
                        $record->update(['is_verified' => false]);
                        Notification::make()->warning()->title('Verification removed')->send();
                    })
                    ->visible(fn (WithdrawalAccount $record) => $record->is_verified && ! $record->hasDisbursements()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}