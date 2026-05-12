<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->placeholder('Leave blank to keep current password'),
                DateTimePicker::make('email_verified_at')
                    ->label('Email verified at'),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('piggy_code')
                    ->label('Piggy Code')
                    ->maxLength(5),
                Select::make('user_type')
                    ->options(UserType::class)
                    ->default('user')
                    ->required(),
            ]);
    }
}