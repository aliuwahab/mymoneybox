<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;

class IdVerificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'idVerifications';

    protected static ?string $title = 'ID Verifications';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'passport'        => 'Passport',
                        'national_card'   => 'Ghana Card',
                        'drivers_license' => "Driver's License",
                        default           => $state,
                    }),
                TextColumn::make('id_number')
                    ->label('ID Number')
                    ->copyable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->date('M j, Y')
                    ->placeholder('—')
                    ->color(fn ($record) => $record?->expires_at?->isPast() ? 'danger' : null),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'      => 'approved',
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);
                        Notification::make()->success()->title('Verification approved')->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('rejection_reason')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_by'      => auth()->id(),
                            'verified_at'      => now(),
                        ]);
                        Notification::make()->warning()->title('Verification rejected')->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
            ]);
    }
}