<?php

namespace App\Filament\Resources\PiggyBoxWithdrawals\Pages;

use App\Filament\Resources\PiggyBoxWithdrawals\PiggyBoxWithdrawalResource;
use Filament\Resources\Pages\ListRecords;

class ListPiggyBoxWithdrawals extends ListRecords
{
    protected static string $resource = PiggyBoxWithdrawalResource::class;
}