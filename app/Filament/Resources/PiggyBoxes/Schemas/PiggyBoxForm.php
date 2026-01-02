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
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->default('My Piggy Box'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('currency_code')
                    ->required(),
                TextInput::make('total_received')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('donation_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
