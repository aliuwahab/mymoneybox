<?php

namespace App\Filament\Widgets;

use App\Models\EventBox;
use App\Models\MoneyBox;
use App\Models\PiggyBox;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CampaignStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $moneyBoxQuery = fn () => MoneyBox::query();
        $piggyBoxQuery = fn () => PiggyBox::query();
        $eventBoxQuery = fn () => EventBox::query();

        $total = $moneyBoxQuery()->count() + $piggyBoxQuery()->count() + $eventBoxQuery()->count();

        $thisMonth = $moneyBoxQuery()->whereDate('created_at', '>=', $startOfMonth)->count()
            + $piggyBoxQuery()->whereDate('created_at', '>=', $startOfMonth)->count()
            + $eventBoxQuery()->whereDate('created_at', '>=', $startOfMonth)->count();

        $thisWeek = $moneyBoxQuery()->whereDate('created_at', '>=', $startOfWeek)->count()
            + $piggyBoxQuery()->whereDate('created_at', '>=', $startOfWeek)->count()
            + $eventBoxQuery()->whereDate('created_at', '>=', $startOfWeek)->count();

        $todayCount = $moneyBoxQuery()->whereDate('created_at', $today)->count()
            + $piggyBoxQuery()->whereDate('created_at', $today)->count()
            + $eventBoxQuery()->whereDate('created_at', $today)->count();

        return [
            Stat::make('Campaigns Today', number_format($todayCount))
                ->description('PiggyBoxes, Wallets & Events created today')
                ->icon('heroicon-o-calendar-days')
                ->color($todayCount > 0 ? 'success' : 'gray'),

            Stat::make('Campaigns This Week', number_format($thisWeek))
                ->description('Created since ' . $startOfWeek->format('D, M j'))
                ->icon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Campaigns This Month', number_format($thisMonth))
                ->description(Carbon::now()->format('F Y'))
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make('Total Campaigns', number_format($total))
                ->description('All PiggyBoxes, Wallets & Events')
                ->icon('heroicon-o-square-3-stack-3d')
                ->color('gray'),
        ];
    }
}