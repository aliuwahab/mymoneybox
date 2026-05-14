<?php

namespace App\Filament\Resources\PiggyBoxWithdrawals;

use App\Filament\Resources\PiggyBoxWithdrawals\Pages\ListPiggyBoxWithdrawals;
use App\Filament\Resources\PiggyBoxWithdrawals\Pages\ViewPiggyBoxWithdrawal;
use App\Filament\Resources\PiggyBoxWithdrawals\Schemas\PiggyBoxWithdrawalInfolist;
use App\Filament\Resources\PiggyBoxWithdrawals\Tables\PiggyBoxWithdrawalsTable;
use App\Models\PiggyBoxWithdrawal;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PiggyBoxWithdrawalResource extends Resource
{
    protected static ?string $model = PiggyBoxWithdrawal::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static string|UnitEnum|null $navigationGroup = 'Withdrawals';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Piggy Wallet Withdrawals';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function infolist(Schema $schema): Schema
    {
        return PiggyBoxWithdrawalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiggyBoxWithdrawalsTable::configure($table);
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
            'index' => ListPiggyBoxWithdrawals::route('/'),
            'view'  => ViewPiggyBoxWithdrawal::route('/{record}'),
        ];
    }
}