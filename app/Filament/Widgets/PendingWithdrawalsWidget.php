<?php

namespace App\Filament\Widgets;

use App\Enums\WithdrawalStatus;
use App\Models\MoneyBoxWithdrawal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingWithdrawalsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Pending Withdrawals';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MoneyBoxWithdrawal::query()
                    ->whereIn('status', [WithdrawalStatus::Pending, WithdrawalStatus::InReview])
                    ->with(['user', 'moneyBox'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('reference')
                    ->label('Reference')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('moneyBox.title')
                    ->label('Box')
                    ->limit(25),
                TextColumn::make('amount')
                    ->money('GHS')
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->label('Net')
                    ->money('GHS'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => WithdrawalStatus::Pending,
                        'info'    => WithdrawalStatus::InReview,
                    ]),
                TextColumn::make('created_at')
                    ->label('Requested')
                    ->since()
                    ->sortable(),
            ])
            ->emptyStateHeading('No pending withdrawals')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->paginated(false);
    }
}