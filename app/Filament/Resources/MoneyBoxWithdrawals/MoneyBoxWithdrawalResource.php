<?php

namespace App\Filament\Resources\MoneyBoxWithdrawals;

use App\Filament\Resources\MoneyBoxWithdrawals\Pages\ListMoneyBoxWithdrawals;
use App\Filament\Resources\MoneyBoxWithdrawals\Pages\ViewMoneyBoxWithdrawal;
use App\Filament\Resources\MoneyBoxWithdrawals\Schemas\MoneyBoxWithdrawalInfolist;
use App\Filament\Resources\MoneyBoxWithdrawals\Tables\MoneyBoxWithdrawalsTable;
use App\Models\MoneyBoxWithdrawal;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MoneyBoxWithdrawalResource extends Resource
{
    protected static ?string $model = MoneyBoxWithdrawal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-circle';

    protected static string|UnitEnum|null $navigationGroup = 'Withdrawals';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'PiggyBox Withdrawals';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function infolist(Schema $schema): Schema
    {
        return MoneyBoxWithdrawalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoneyBoxWithdrawalsTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereIn('status', ['pending', 'in_review'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMoneyBoxWithdrawals::route('/'),
            'view'  => ViewMoneyBoxWithdrawal::route('/{record}'),
        ];
    }
}