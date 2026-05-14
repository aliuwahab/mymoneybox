<?php

namespace App\Filament\Resources\MoneyBoxWithdrawals\Tables;

use App\Actions\DisburseWithdrawalAction;
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

class MoneyBoxWithdrawalsTable
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
                TextColumn::make('moneyBox.title')
                    ->label('PiggyBox')
                    ->limit(25)
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('fee')
                    ->money('GHS')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('withdrawalAccount.account_name')
                    ->label('Account')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => WithdrawalStatus::Pending,
                        'info'    => WithdrawalStatus::InReview,
                        'success' => WithdrawalStatus::Approved,
                        'purple'  => WithdrawalStatus::Processing,
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
                Action::make('addNote')
                    ->label('Comment')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->form([
                        Textarea::make('note')
                            ->label('Comment')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->notes()->create([
                            'user_id' => auth()->id(),
                            'note' => $data['note'],
                            'is_admin' => true,
                        ]);
                        Notification::make()->success()->title('Comment added')->send();
                    }),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Withdrawal')
                    ->modalDescription('This will mark the withdrawal as approved and ready for disbursement.')
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
                    ->modalHeading('Reject Withdrawal')
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
                Action::make('voidAndDelete')
                    ->label('Void & Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Void & Delete Withdrawal')
                    ->modalDescription('This permanently deletes the withdrawal record and restores the available balance. Use only to correct erroneous disbursed records.')
                    ->action(function ($record) {
                        $record->delete();
                        Notification::make()->success()->title('Withdrawal deleted — balance restored')->send();
                    })
                    ->visible(fn () => auth()->user()?->email === 'aliuwahab@gmail.com'),

                Action::make('disburse')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Send Funds to User')
                    ->modalDescription('This will submit the transfer to the payment provider. The status will update to Disbursed once the payment provider confirms.')
                    ->action(function ($record) {
                        $result = app(DisburseWithdrawalAction::class)->execute($record);

                        if ($result['success']) {
                            Notification::make()->success()->title('Transfer submitted')->body('Awaiting payment provider confirmation.')->send();
                        } else {
                            Notification::make()->danger()->title('Transfer failed')->body($result['message'] ?? 'Please try again.')->send();
                        }
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
