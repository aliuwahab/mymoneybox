<?php

namespace App\Filament\Resources\Contributions\Tables;

use App\Enums\PaymentStatus;
use App\Models\Contribution;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
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
                    ->label('PiggyBox')
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
                    ->color(fn (PaymentStatus $state) => match ($state) {
                        PaymentStatus::Completed => 'success',
                        PaymentStatus::Pending   => 'warning',
                        PaymentStatus::Failed    => 'danger',
                        PaymentStatus::Refunded  => 'gray',
                    })
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
                Action::make('resendReceipt')
                    ->label('Resend Receipt')
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Resend Contribution Receipt')
                    ->modalDescription('This will queue the thank-you receipt email to be sent again to the contributor.')
                    ->action(function (Contribution $record) {
                        $record->update([
                            'receipt_resent_at'    => now(),
                            'receipt_resend_count' => $record->receipt_resend_count + 1,
                        ]);
                        event(new \App\Events\ContributionProcessed($record, $record->moneyBox));
                        Notification::make()->success()->title('Receipt queued for resend')->send();
                    })
                    ->visible(fn (Contribution $record) => $record->payment_status === PaymentStatus::Completed),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
