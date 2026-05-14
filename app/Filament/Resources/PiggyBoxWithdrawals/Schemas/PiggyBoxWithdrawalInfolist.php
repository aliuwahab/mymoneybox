<?php

namespace App\Filament\Resources\PiggyBoxWithdrawals\Schemas;

use App\Enums\WithdrawalStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PiggyBoxWithdrawalInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Withdrawal Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('reference')
                            ->copyable()
                            ->fontFamily('mono'),
                        TextEntry::make('status')
                            ->badge()
                            ->colors([
                                'warning' => WithdrawalStatus::Pending,
                                'info'    => WithdrawalStatus::InReview,
                                'success' => WithdrawalStatus::Approved,
                                'primary' => WithdrawalStatus::Disbursed,
                                'danger'  => WithdrawalStatus::Rejected,
                                'gray'    => WithdrawalStatus::Failed,
                            ]),
                        TextEntry::make('currency_code')
                            ->label('Currency'),
                        TextEntry::make('amount')
                            ->money('GHS'),
                        TextEntry::make('fee')
                            ->money('GHS'),
                        TextEntry::make('net_amount')
                            ->label('Net Amount')
                            ->money('GHS'),
                        TextEntry::make('user_note')
                            ->label('User Note')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('rejection_reason')
                            ->placeholder('—')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->status === WithdrawalStatus::Rejected),
                        TextEntry::make('failure_reason')
                            ->placeholder('—')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->status === WithdrawalStatus::Failed),
                    ]),

                Section::make('Owner & Piggy Wallet')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('User'),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('piggyBox.title')
                            ->label('Piggy Wallet'),
                        TextEntry::make('piggyBox.total_received')
                            ->label('Piggy Wallet Balance')
                            ->money('GHS'),
                    ]),

                Section::make('Payout Account')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('withdrawalAccount.account_type')
                            ->label('Type')
                            ->badge(),
                        TextEntry::make('withdrawalAccount.account_name')
                            ->label('Account Name'),
                        TextEntry::make('withdrawalAccount.account_number')
                            ->label('Account Number')
                            ->copyable(),
                        TextEntry::make('withdrawalAccount.mobile_network')
                            ->label('Network')
                            ->placeholder('—'),
                        TextEntry::make('withdrawalAccount.bank_name')
                            ->label('Bank')
                            ->placeholder('—'),
                    ]),

                Section::make('Processing')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('payment_provider')
                            ->placeholder('—'),
                        TextEntry::make('transaction_reference')
                            ->label('Transaction Ref')
                            ->placeholder('—')
                            ->copyable(),
                        TextEntry::make('processedBy.name')
                            ->label('Processed By')
                            ->placeholder('—'),
                        TextEntry::make('processed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('disbursed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label('Requested At')
                            ->dateTime(),
                    ]),
            ]);
    }
}
