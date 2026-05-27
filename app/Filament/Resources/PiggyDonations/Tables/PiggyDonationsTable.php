<?php

namespace App\Filament\Resources\PiggyDonations\Tables;

use App\Actions\VerifyPiggyDonationPaymentAction;
use App\Enums\PaymentStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PiggyDonationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('piggyBox.title')
                    ->label('Piggy Wallet')
                    ->limit(25)
                    ->searchable(),
                TextColumn::make('donor_name')
                    ->label('Donor')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous' : ($state ?? '—'))
                    ->searchable(),
                TextColumn::make('donor_email')
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
                        'danger' => PaymentStatus::Failed,
                        'gray' => PaymentStatus::Refunded,
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
                TextColumn::make('transaction_rrn')
                    ->label('RRN')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('credited_at')
                    ->label('Credited')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('receipt_sent_at')
                    ->label('Receipt')
                    ->dateTime()
                    ->placeholder('Not sent')
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
                Action::make('verifyPayment')
                    ->label('Verify')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Verify Piggy Wallet Gift')
                    ->modalDescription('This checks the payment provider and credits the wallet only if the payment is confirmed for the expected amount.')
                    ->action(function ($record, VerifyPiggyDonationPaymentAction $action) {
                        $donation = $action->execute($record, auth()->id());

                        if ($donation->payment_status === PaymentStatus::Completed) {
                            Notification::make()->success()->title('Gift verified and credited')->send();
                        } else {
                            Notification::make()->warning()->title('Gift is still not completed')->send();
                        }
                    })
                    ->visible(fn ($record) => in_array($record->payment_status, [PaymentStatus::Pending, PaymentStatus::Failed], true)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
