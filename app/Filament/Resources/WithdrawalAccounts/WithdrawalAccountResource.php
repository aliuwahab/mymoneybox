<?php

namespace App\Filament\Resources\WithdrawalAccounts;

use App\Filament\Resources\WithdrawalAccounts\Pages\ListWithdrawalAccounts;
use App\Filament\Resources\WithdrawalAccounts\Pages\ViewWithdrawalAccount;
use App\Filament\Resources\WithdrawalAccounts\Schemas\WithdrawalAccountInfolist;
use App\Filament\Resources\WithdrawalAccounts\Tables\WithdrawalAccountsTable;
use App\Models\WithdrawalAccount;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WithdrawalAccountResource extends Resource
{
    protected static ?string $model = WithdrawalAccount::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|UnitEnum|null $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Withdrawal Accounts';

    protected static ?string $modelLabel = 'Withdrawal Account';

    protected static ?string $pluralModelLabel = 'Withdrawal Accounts';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'account_name';

    public static function getNavigationBadge(): ?string
    {
        return (string) WithdrawalAccount::where('is_verified', false)
            ->where('is_active', true)
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function infolist(Schema $schema): Schema
    {
        return WithdrawalAccountInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WithdrawalAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWithdrawalAccounts::route('/'),
            'view'  => ViewWithdrawalAccount::route('/{record}'),
        ];
    }
}