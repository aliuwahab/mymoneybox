<?php

namespace App\Filament\Resources\MoneyBoxes\Schemas;

use App\Models\MoneyBox;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MoneyBoxInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Box Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Owner'),
                        TextEntry::make('category.name')
                            ->label('Category')
                            ->placeholder('—'),
                        TextEntry::make('currency_code')
                            ->label('Currency'),
                        TextEntry::make('title')
                            ->columnSpan(2),
                        TextEntry::make('slug')
                            ->copyable()
                            ->url(fn ($record) => $record->getPublicUrl())
                            ->openUrlInNewTab(),
                        TextEntry::make('description')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),

                Section::make('Settings')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('visibility')
                            ->badge(),
                        TextEntry::make('contributor_identity')
                            ->badge(),
                        TextEntry::make('amount_type')
                            ->badge(),
                        TextEntry::make('goal_amount')
                            ->money('GHS')
                            ->placeholder('No goal'),
                        TextEntry::make('fixed_amount')
                            ->money('GHS')
                            ->placeholder('—'),
                        TextEntry::make('minimum_amount')
                            ->money('GHS')
                            ->placeholder('—'),
                        TextEntry::make('maximum_amount')
                            ->money('GHS')
                            ->placeholder('—'),
                        TextEntry::make('start_date')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('end_date')
                            ->dateTime()
                            ->placeholder('—'),
                        IconEntry::make('is_ongoing')
                            ->label('Ongoing')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                    ]),

                Section::make('Stats')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('total_contributions')
                            ->label('Total Raised')
                            ->money('GHS'),
                        TextEntry::make('contribution_count')
                            ->label('Contributions')
                            ->numeric(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->placeholder('—')
                            ->visible(fn (MoneyBox $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}