<?php

namespace App\Filament\Resources\MoneyBoxes\Schemas;

use App\Models\MoneyBox;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MoneyBoxInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('category.name')
                    ->label('Category')
                    ->placeholder('-'),
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('goal_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('currency_code'),
                TextEntry::make('visibility')
                    ->badge(),
                TextEntry::make('contributor_identity')
                    ->badge(),
                TextEntry::make('amount_type')
                    ->badge(),
                TextEntry::make('fixed_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('minimum_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('maximum_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('start_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_ongoing')
                    ->boolean(),
                TextEntry::make('qr_code_path')
                    ->placeholder('-'),
                TextEntry::make('total_contributions')
                    ->numeric(),
                TextEntry::make('contribution_count')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (MoneyBox $record): bool => $record->trashed()),
            ]);
    }
}
