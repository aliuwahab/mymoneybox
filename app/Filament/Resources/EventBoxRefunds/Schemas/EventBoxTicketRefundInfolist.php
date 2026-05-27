<?php

namespace App\Filament\Resources\EventBoxRefunds\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EventBoxTicketRefundInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Refund Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reference')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('ticket.eventBox.title')
                            ->label('Event'),
                        TextEntry::make('ticket.buyer_name')
                            ->label('Buyer'),
                        TextEntry::make('ticket.buyer_email')
                            ->label('Email'),
                        TextEntry::make('ticket.code')
                            ->label('Ticket code')
                            ->copyable(),
                    ]),

                Section::make('Financials')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('gross_amount')
                            ->label('Amount paid')
                            ->money('GHS'),
                        TextEntry::make('charge_amount')
                            ->label('Charge')
                            ->money('GHS'),
                        TextEntry::make('refund_amount')
                            ->label('Refund amount')
                            ->money('GHS'),
                        TextEntry::make('currency_code')
                            ->label('Currency'),
                    ]),

                Section::make('Recipient')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('recipient_name')
                            ->placeholder('—'),
                        TextEntry::make('recipient_account_number')
                            ->label('Account number')
                            ->copyable(),
                        TextEntry::make('recipient_network')
                            ->label('Network'),
                        TextEntry::make('payment_provider')
                            ->label('Provider'),
                        TextEntry::make('transaction_reference')
                            ->label('Transaction reference')
                            ->copyable()
                            ->placeholder('—'),
                    ]),

                Section::make('Lifecycle')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('reason')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('processed_at')
                            ->label('Submitted at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('completed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('failed_at')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('failure_reason')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Requested at')
                            ->dateTime(),
                    ]),

                Section::make('Provider Metadata')
                    ->collapsed()
                    ->schema([
                        TextEntry::make('payment_metadata')
                            ->label('')
                            ->formatStateUsing(fn ($state) => is_array($state)
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                : ($state ?? '—'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}