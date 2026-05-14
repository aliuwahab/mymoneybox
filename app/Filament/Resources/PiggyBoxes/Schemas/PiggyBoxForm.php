<?php

namespace App\Filament\Resources\PiggyBoxes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PiggyBoxForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->default('My Piggy Wallet'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('currency_code')
                    ->required()
                    ->default('GHS'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                TextInput::make('fee_percentage')
                    ->label('Fee override (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->placeholder(config('withdrawal.fee_percentage', 2.5))
                    ->helperText('Leave blank to use the global default (' . config('withdrawal.fee_percentage', 2.5) . '%). Set a custom percentage to override for this Piggy Wallet only.'),
            ]);
    }
}
