<?php

namespace App\Filament\Resources\EventBoxes\RelationManagers;

use App\Actions\VerifyEventTicketPaymentAction;
use App\Enums\PaymentStatus;
use App\Enums\TicketStatus;
use App\Events\TicketIssued;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
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
            ])
            ->recordActions([
                Action::make('verifyPayment')
                    ->label('Verify payment')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Verify Ticket Payment')
                    ->modalDescription('Checks the payment provider and completes the ticket(s) if the payment is confirmed. Group purchases are completed together.')
                    ->action(function ($record, VerifyEventTicketPaymentAction $action) {
                        $result = $action->execute($record);

                        if ($result['success'] && $result['completed'] > 0) {
                            Notification::make()->success()->title($result['message'])->send();
                        } elseif ($result['success']) {
                            Notification::make()->info()->title($result['message'])->send();
                        } else {
                            Notification::make()->warning()->title($result['message'])->send();
                        }
                    })
                    ->visible(fn ($record) => $record->payment_status === PaymentStatus::Pending),

                Action::make('resendEmail')
                    ->label('Resend ticket email')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Resend Ticket Email')
                    ->modalDescription('This will queue the ticket email to be sent again to the buyer\'s email address.')
                    ->action(function ($record) {
                        $record->update([
                            'ticket_email_sending_at' => null,
                            'ticket_email_sent_at'    => null,
                        ]);

                        event(new TicketIssued($record->fresh(['eventBox'])));

                        Notification::make()->success()->title('Ticket email queued for resend')->send();
                    })
                    ->visible(fn ($record) => $record->payment_status === PaymentStatus::Completed && $record->code !== null),
            ]);
    }
}