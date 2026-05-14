<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Contribution;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentContributionsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Contributions';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Contribution::query()
                    ->where('payment_status', PaymentStatus::Completed)
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('moneyBox.title')
                    ->label('PiggyBox')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('contributor_name')
                    ->label('Contributor')
                    ->formatStateUsing(fn ($state, $record) => $record->is_anonymous ? 'Anonymous' : ($state ?? '—'))
                    ->searchable(),
                TextColumn::make('contributor_email')
                    ->label('Email')
                    ->searchable()
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
                    ]),
                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
