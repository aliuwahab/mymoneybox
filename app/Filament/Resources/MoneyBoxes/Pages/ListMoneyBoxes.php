<?php

namespace App\Filament\Resources\MoneyBoxes\Pages;

use App\Filament\Resources\MoneyBoxes\MoneyBoxResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMoneyBoxes extends ListRecords
{
    protected static string $resource = MoneyBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
