<?php

namespace App\Filament\Resources\WithdrawalAccounts\Pages;

use App\Filament\Resources\WithdrawalAccounts\WithdrawalAccountResource;
use Filament\Resources\Pages\ListRecords;

class ListWithdrawalAccounts extends ListRecords
{
    protected static string $resource = WithdrawalAccountResource::class;
}