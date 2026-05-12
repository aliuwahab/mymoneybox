<?php

namespace App\Filament\Resources\MoneyBoxWithdrawals\Pages;

use App\Filament\Resources\MoneyBoxWithdrawals\MoneyBoxWithdrawalResource;
use Filament\Resources\Pages\ListRecords;

class ListMoneyBoxWithdrawals extends ListRecords
{
    protected static string $resource = MoneyBoxWithdrawalResource::class;
}