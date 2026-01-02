<?php

namespace App\Filament\Resources\Contributions\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('money_box_id')
                    ->relationship('moneyBox', 'title')
                    ->required(),
                TextInput::make('contributor_name'),
                TextInput::make('contributor_email')
                    ->email(),
                TextInput::make('contributor_phone')
                    ->tel(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency_code')
                    ->required(),
                Toggle::make('is_anonymous')
                    ->required(),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('payment_provider'),
                TextInput::make('payment_method'),
                TextInput::make('payment_reference'),
                Select::make('payment_status')
                    ->options(PaymentStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('ip_address'),
                Textarea::make('user_agent')
                    ->columnSpanFull(),
                TextInput::make('transaction_rrn'),
                Textarea::make('payment_metadata')
                    ->columnSpanFull(),
            ]);
    }
}
