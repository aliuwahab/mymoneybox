<?php

namespace App\Filament\Resources\EventBoxes\Tables;

use App\Enums\EventBoxStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EventBoxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('event_date', 'asc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(35),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (EventBoxStatus $state) => match($state) {
                        EventBoxStatus::Active    => 'success',
                        EventBoxStatus::Draft     => 'gray',
                        EventBoxStatus::SoldOut   => 'warning',
                        EventBoxStatus::Ended     => 'gray',
                        EventBoxStatus::Cancelled => 'danger',
                    })
                    ->formatStateUsing(fn (EventBoxStatus $state) => $state->label()),
                TextColumn::make('event_date')
                    ->label('Event date')
                    ->dateTime('M j, Y g:ia')
                    ->sortable(),
                TextColumn::make('venue')
                    ->limit(25)
                    ->toggleable(),
                TextColumn::make('tickets_sold')
                    ->label('Tickets sold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('capacity')
                    ->numeric()
                    ->default('Unlimited')
                    ->toggleable(),
                TextColumn::make('contact_email')
                    ->label('Contact email')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_phone')
                    ->label('Contact phone')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(collect(EventBoxStatus::cases())->mapWithKeys(
                        fn ($case) => [$case->value => $case->label()]
                    )->toArray()),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}