<?php

namespace App\Filament\Resources\Contributions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContributionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('moneyBox.title')
                    ->label('Money box'),
                TextEntry::make('contributor_name')
                    ->placeholder('-'),
                TextEntry::make('contributor_email')
                    ->placeholder('-'),
                TextEntry::make('contributor_phone')
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('currency_code'),
                IconEntry::make('is_anonymous')
                    ->boolean(),
                TextEntry::make('message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('payment_provider')
                    ->placeholder('-'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                TextEntry::make('payment_reference')
                    ->placeholder('-'),
                TextEntry::make('payment_status')
                    ->badge(),
                TextEntry::make('ip_address')
                    ->placeholder('-'),
                TextEntry::make('user_agent')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('transaction_rrn')
                    ->placeholder('-'),
                TextEntry::make('payment_metadata')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
