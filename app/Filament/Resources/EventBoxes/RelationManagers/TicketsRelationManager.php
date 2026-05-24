<?php

namespace App\Filament\Resources\EventBoxes\RelationManagers;

use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TicketsRelationManager extends RelationManager
{
    protected static string $relationship = 'tickets';

    protected static ?string $title = 'Attendees / Tickets';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->label('Ticket code')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('buyer_name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('buyer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('buyer_phone')
                    ->label('Phone')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ticket_type_name')
                    ->label('Ticket type'),
                TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (PaymentStatus $state) => match($state) {
                        PaymentStatus::Completed => 'success',
                        PaymentStatus::Pending   => 'warning',
                        PaymentStatus::Failed    => 'danger',
                        PaymentStatus::Refunded  => 'gray',
                    })
                    ->formatStateUsing(fn (PaymentStatus $state) => ucfirst($state->value)),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (TicketStatus $state) => match($state) {
                        TicketStatus::Unused   => 'success',
                        TicketStatus::Redeemed => 'info',
                        TicketStatus::Voided   => 'danger',
                    })
                    ->formatStateUsing(fn (TicketStatus $state) => $state->label()),
                TextColumn::make('redeemed_at')
                    ->label('Redeemed')
                    ->dateTime('M j, Y g:ia')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Purchased')
                    ->dateTime('M j, Y g:ia')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->options([
                        'pending'   => 'Pending',
                        'completed' => 'Completed',
                        'failed'    => 'Failed',
                        'refunded'  => 'Refunded',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'unused'   => 'Unused',
                        'redeemed' => 'Redeemed',
                        'voided'   => 'Voided',
                    ]),
            ]);
    }
}