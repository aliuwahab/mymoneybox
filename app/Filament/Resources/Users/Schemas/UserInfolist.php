<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Email address')
                            ->copyable(),
                        TextEntry::make('user_type')
                            ->badge()
                            ->color(fn ($state) => match (is_string($state) ? $state : $state->value) {
                                'admin' => 'danger',
                                'user'  => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('country.name')
                            ->label('Country')
                            ->placeholder('—'),
                        TextEntry::make('piggy_code')
                            ->label('Piggy Code')
                            ->copyable()
                            ->placeholder('—'),
                        TextEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->dateTime()
                            ->placeholder('Not verified'),
                    ]),

                Section::make('Activity')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('moneyBoxes_count')
                            ->label('PiggyBoxes')
                            ->state(fn ($record) => $record->moneyBoxes()->count()),
                        TextEntry::make('piggyBox.total_received')
                            ->label('Piggy Wallet Balance')
                            ->money('GHS')
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label('Joined')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                        TextEntry::make('deleted_at')
                            ->label('Banned At')
                            ->dateTime()
                            ->placeholder('Active')
                            ->visible(fn ($record) => $record->trashed()),
                    ]),
            ]);
    }
}