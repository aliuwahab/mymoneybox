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
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('currency_code')
                    ->required()
                    ->default('GHS'),
                TextInput::make('goal_amount')
                    ->numeric()
                    ->minValue(0),
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
                    ->numeric()
                    ->minValue(0),
                TextInput::make('minimum_amount')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('maximum_amount')
                    ->numeric()
                    ->minValue(0),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
                Toggle::make('is_ongoing')
                    ->label('No end date (ongoing)')
                    ->default(false),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Toggle::make('is_featured')
                    ->label('Featured on homepage')
                    ->helperText('Only one box should be featured at a time. Enabling this on a new box will not auto-disable others — unset the previous manually.')
                    ->default(false),
                TextInput::make('fee_percentage')
                    ->label('Fee override (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->placeholder(config('withdrawal.fee_percentage', 2.5))
                    ->helperText('Leave blank to use the global default (' . config('withdrawal.fee_percentage', 2.5) . '%). Set a custom percentage (e.g. 0 for fee-free) to override for this PiggyBox only.'),
            ]);
    }
}
