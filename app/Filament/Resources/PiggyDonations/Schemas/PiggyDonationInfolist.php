<?php

namespace App\Filament\Resources\PiggyDonations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PiggyDonationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('piggyBox.title')
                    ->label('Piggy box'),
                TextEntry::make('donor_name'),
                TextEntry::make('donor_email'),
                TextEntry::make('donor_phone')
                    ->placeholder('-'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('currency_code'),
                IconEntry::make('is_anonymous')
                    ->boolean(),
                TextEntry::make('message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('payment_provider'),
                TextEntry::make('payment_method')
                    ->placeholder('-'),
                TextEntry::make('payment_reference'),
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
            ]);
    }
}
