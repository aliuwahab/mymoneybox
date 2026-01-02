<?php

namespace App\Filament\Resources\MoneyBoxes\Schemas;

use App\Enums\AmountType;
use App\Enums\ContributorIdentity;
use App\Enums\Visibility;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MoneyBoxForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('goal_amount')
                    ->numeric(),
                TextInput::make('currency_code')
                    ->required(),
                Select::make('visibility')
                    ->options(Visibility::class)
                    ->default('public')
                    ->required(),
                Select::make('contributor_identity')
                    ->options(ContributorIdentity::class)
                    ->default('user_choice')
                    ->required(),
                Select::make('amount_type')
                    ->options(AmountType::class)
                    ->default('variable')
                    ->required(),
                TextInput::make('fixed_amount')
                    ->numeric(),
                TextInput::make('minimum_amount')
                    ->numeric(),
                TextInput::make('maximum_amount')
                    ->numeric(),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
                Toggle::make('is_ongoing')
                    ->required(),
                TextInput::make('qr_code_path'),
                TextInput::make('total_contributions')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('contribution_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
