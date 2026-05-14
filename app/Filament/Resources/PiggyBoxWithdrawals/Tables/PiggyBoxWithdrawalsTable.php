<?php

namespace App\Filament\Resources\PiggyBoxWithdrawals\Tables;

use App\Enums\WithdrawalStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PiggyBoxWithdrawalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('piggyBox.title')
                    ->label('Piggy Wallet')
                    ->limit(25)
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->money('GHS'),
                TextColumn::make('withdrawalAccount.account_name')
                    ->label('Account')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => WithdrawalStatus::Pending,
                        'info'    => WithdrawalStatus::InReview,
                        'success' => WithdrawalStatus::Approved,
                        'primary' => WithdrawalStatus::Disbursed,
                        'danger'  => WithdrawalStatus::Rejected,
                        'gray'    => WithdrawalStatus::Failed,
                    ])
                    ->sortable(),
                TextColumn::make('processedBy.name')
                    ->label('Processed By')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(WithdrawalStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status'       => WithdrawalStatus::Approved,
                            'processed_by' => auth()->id(),
                            'processed_at' => now(),
                        ]);
                        Notification::make()->success()->title('Withdrawal approved')->send();
                    })
                    ->visible(fn ($record) => $record->canBeApproved()),
                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'           => WithdrawalStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                            'processed_by'     => auth()->id(),
                            'processed_at'     => now(),
                        ]);
                        Notification::make()->warning()->title('Withdrawal rejected')->send();
                    })
                    ->visible(fn ($record) => $record->canBeRejected()),
                Action::make('disburse')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Mark as Disbursed')
                    ->modalDescription('Confirm that funds have been sent to the user\'s account.')
                    ->action(function ($record) {
                        $record->update([
                            'status'       => WithdrawalStatus::Disbursed,
                            'disbursed_at' => now(),
                        ]);
                        Notification::make()->success()->title('Marked as disbursed')->send();
                    })
                    ->visible(fn ($record) => $record->canBeDisbursed()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}