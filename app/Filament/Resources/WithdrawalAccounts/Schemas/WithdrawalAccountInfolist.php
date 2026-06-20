<?php

namespace App\Filament\Resources\WithdrawalAccounts\Schemas;

use App\Enums\AccountType;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WithdrawalAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Account Owner'),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('account_type')
                            ->badge()
                            ->color(fn (AccountType $state) => match ($state) {
                                AccountType::MobileMoney => 'success',
                                AccountType::Bank        => 'info',
                            })
                            ->formatStateUsing(fn (AccountType $state) => $state->value === 'mobile_money' ? 'Mobile Money' : 'Bank'),
                        TextEntry::make('account_name')
                            ->label('Account Name'),
                        TextEntry::make('account_number')
                            ->label('Account Number')
                            ->copyable(),
                        TextEntry::make('mobile_network')
                            ->label('Mobile Network')
                            ->formatStateUsing(fn ($state) => $state ? strtoupper($state->value) : '—')
                            ->placeholder('—')
                            ->visible(fn ($record) => $record->account_type === AccountType::MobileMoney),
                        TextEntry::make('bank_name')
                            ->label('Bank')
                            ->placeholder('—')
                            ->visible(fn ($record) => $record->account_type === AccountType::Bank),
                        TextEntry::make('bank_branch')
                            ->label('Branch')
                            ->placeholder('—')
                            ->visible(fn ($record) => $record->account_type === AccountType::Bank),
                    ]),

                Section::make('Status')
                    ->columns(3)
                    ->schema([
                        IconEntry::make('is_verified')
                            ->label('Verified')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        IconEntry::make('is_default')
                            ->label('Default Account')
                            ->boolean(),
                        TextEntry::make('created_at')
                            ->label('Added')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ]),
            ]);
    }
}