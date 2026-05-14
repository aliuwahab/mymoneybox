<?php

namespace App\Filament\Resources\TrustedLogos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrustedLogoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Display name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Shown in the homepage trusted-by strip.'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),
                Toggle::make('is_active')
                    ->label('Show on homepage')
                    ->default(true),
            ]);
    }
}
