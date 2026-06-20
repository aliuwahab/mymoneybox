<?php

namespace App\Filament\Resources\IdVerifications\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IdVerificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identity')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Account Holder'),
                        TextEntry::make('user.email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge()
                            ->colors([
                                'warning' => 'pending',
                                'success' => 'approved',
                                'danger'  => 'rejected',
                            ]),
                        TextEntry::make('first_name'),
                        TextEntry::make('last_name'),
                        TextEntry::make('other_names')
                            ->placeholder('—'),
                        TextEntry::make('id_type')
                            ->label('ID Type')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'passport'       => 'Passport',
                                'national_card'  => 'Ghana Card',
                                'drivers_license' => "Driver's License",
                                default          => $state,
                            }),
                        TextEntry::make('id_number')
                            ->label('ID Number')
                            ->copyable()
                            ->placeholder('—'),
                        TextEntry::make('expires_at')
                            ->label('ID Expires')
                            ->date()
                            ->placeholder('—'),
                    ]),

                Section::make('ID Documents')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('front_image')
                            ->label('Front of ID')
                            ->state(fn ($record) => $record->getFrontImageUrl())
                            ->height(220)
                            ->placeholder('No front image uploaded'),
                        ImageEntry::make('back_image')
                            ->label('Back of ID')
                            ->state(fn ($record) => $record->getBackImageUrl())
                            ->height(220)
                            ->placeholder('No back image uploaded'),
                    ]),

                Section::make('Review')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('verifiedBy.name')
                            ->label('Reviewed By')
                            ->placeholder('Not yet reviewed'),
                        TextEntry::make('verified_at')
                            ->label('Reviewed At')
                            ->dateTime()
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                        TextEntry::make('rejection_reason')
                            ->placeholder('—')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->status === 'rejected'),
                    ]),
            ]);
    }
}