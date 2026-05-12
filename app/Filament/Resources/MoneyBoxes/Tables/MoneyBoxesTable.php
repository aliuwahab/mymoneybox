<?php

namespace App\Filament\Resources\MoneyBoxes\Tables;

use App\Enums\Visibility;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MoneyBoxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('total_contributions')
                    ->label('Raised')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('goal_amount')
                    ->label('Goal')
                    ->money('GHS')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('contribution_count')
                    ->label('Contributors')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('visibility')
                    ->badge()
                    ->colors([
                        'success' => Visibility::Public,
                        'warning' => Visibility::Private,
                    ]),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Ends')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('visibility')
                    ->options(Visibility::class),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('deactivate')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => false]);
                        Notification::make()->warning()->title('Box deactivated')->send();
                    })
                    ->visible(fn ($record) => $record->is_active && !$record->trashed()),
                Action::make('activate')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => true]);
                        Notification::make()->success()->title('Box activated')->send();
                    })
                    ->visible(fn ($record) => !$record->is_active && !$record->trashed()),
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