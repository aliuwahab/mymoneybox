<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Enums\WithdrawalStatus;
use App\Models\Contribution;
use App\Models\IdVerification;
use App\Models\MoneyBox;
use App\Models\MoneyBoxWithdrawal;
use App\Models\PiggyBoxWithdrawal;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayContributions = Contribution::query()
            ->where('payment_status', PaymentStatus::Completed)
            ->whereDate('created_at', today())
            ->sum('amount');

        $pendingWithdrawals = MoneyBoxWithdrawal::query()
            ->whereIn('status', [WithdrawalStatus::Pending, WithdrawalStatus::InReview])
            ->count()
            + PiggyBoxWithdrawal::query()
            ->whereIn('status', [WithdrawalStatus::Pending, WithdrawalStatus::InReview])
            ->count();

        $pendingVerifications = IdVerification::query()
            ->where('status', 'pending')
            ->count();

        $totalRevenue = Contribution::query()
            ->where('payment_status', PaymentStatus::Completed)
            ->sum('amount');

        return [
            Stat::make('Total Users', number_format(User::count()))
                ->description('Registered accounts')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active PiggyBoxes', number_format(MoneyBox::where('is_active', true)->count()))
                ->description('Live fundraising PiggyBoxes')
                ->icon('heroicon-o-gift')
                ->color('success'),

            Stat::make("Today's Collections", 'GHS ' . number_format($todayContributions, 2))
                ->description('Completed contributions today')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('All-Time Revenue', 'GHS ' . number_format($totalRevenue, 2))
                ->description('Total completed contributions')
                ->icon('heroicon-o-banknotes')
                ->color('info'),

            Stat::make('Pending Withdrawals', number_format($pendingWithdrawals))
                ->description('Awaiting review or approval')
                ->icon('heroicon-o-arrow-up-circle')
                ->color($pendingWithdrawals > 0 ? 'warning' : 'gray'),

            Stat::make('Pending Verifications', number_format($pendingVerifications))
                ->description('ID verifications to review')
                ->icon('heroicon-o-shield-check')
                ->color($pendingVerifications > 0 ? 'danger' : 'gray'),
        ];
    }
}
