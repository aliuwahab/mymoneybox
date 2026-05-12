<?php

namespace App\Filament\Resources\Contributions\Tables;

use App\Enums\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('moneyBox.title')
                    ->label('Box')
                    ->limit(25)
                    ->searchable(),
                TextColumn::make('contributor_name')
                    ->label('Name')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous' : ($state ?? '—'))
                    ->searchable(),
                TextColumn::make('contributor_email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->colors([
                        'success' => PaymentStatus::Completed,
                        'warning' => PaymentStatus::Pending,
                        'danger'  => PaymentStatus::Failed,
                        'gray'    => PaymentStatus::Refunded,
                    ])
                    ->sortable(),
                TextColumn::make('payment_reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_anonymous')
                    ->label('Anon')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options(PaymentStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}