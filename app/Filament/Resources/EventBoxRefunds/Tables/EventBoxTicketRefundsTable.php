<?php

namespace App\Filament\Resources\EventBoxRefunds\Tables;

use App\Actions\ProcessEventBoxTicketRefundAction;
use App\Enums\RefundStatus;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventBoxTicketRefundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['ticket.eventBox']))
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('ticket.eventBox.title')
                    ->label('Event')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('ticket.buyer_name')
                    ->label('Buyer')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (RefundStatus $state) => match ($state) {
                        RefundStatus::Pending    => 'warning',
                        RefundStatus::Processing => 'info',
                        RefundStatus::Completed  => 'success',
                        RefundStatus::Failed     => 'danger',
                    })
                    ->formatStateUsing(fn (RefundStatus $state) => ucfirst($state->value)),
                TextColumn::make('gross_amount')
                    ->label('Paid')
                    ->money('GHS'),
                TextColumn::make('refund_amount')
                    ->label('Refund')
                    ->money('GHS'),
                TextColumn::make('recipient_account_number')
                    ->label('Account')
                    ->toggleable(),
                TextColumn::make('recipient_network')
                    ->label('Network')
                    ->toggleable(),
                TextColumn::make('failure_reason')
                    ->label('Failure reason')
                    ->placeholder('—')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime('M j, Y g:ia')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'completed'  => 'Completed',
                        'failed'     => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Retry Refund')
                    ->modalDescription('This will re-submit the refund to the payment provider. Verify the recipient details are still correct before retrying.')
                    ->action(function ($record, ProcessEventBoxTicketRefundAction $processAction) {
                        $record->update([
                            'status'         => RefundStatus::Pending,
                            'failed_at'      => null,
                            'failure_reason' => null,
                        ]);

                        $result = $processAction->execute($record->fresh());

                        if ($result['success']) {
                            Notification::make()->success()->title('Refund re-submitted to provider')->send();
                        } else {
                            Notification::make()->warning()->title($result['message'] ?? 'Retry failed')->send();
                        }
                    })
                    ->visible(fn ($record) => $record->status === RefundStatus::Failed),
            ]);
    }
}