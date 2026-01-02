<?php

namespace App\Filament\Resources\MoneyBoxes\Pages;

use App\Filament\Resources\MoneyBoxes\MoneyBoxResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMoneyBox extends ViewRecord
{
    protected static string $resource = MoneyBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
