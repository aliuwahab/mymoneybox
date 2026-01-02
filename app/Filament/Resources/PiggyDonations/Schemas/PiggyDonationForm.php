<?php

namespace App\Filament\Resources\PiggyDonations\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PiggyDonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('piggy_box_id')
                    ->relationship('piggyBox', 'title')
                    ->required(),
                TextInput::make('donor_name')
                    ->required(),
                TextInput::make('donor_email')
                    ->email()
                    ->required(),
                TextInput::make('donor_phone')
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
                TextInput::make('payment_provider')
                    ->required()
                    ->default('trendipay'),
                TextInput::make('payment_method'),
                TextInput::make('payment_reference')
                    ->required(),
                Select::make('payment_status')
                    ->options(PaymentStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('ip_address'),
                Textarea::make('user_agent')
                    ->columnSpanFull(),
            ]);
    }
}
