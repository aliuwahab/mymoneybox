<?php

namespace App\Filament\Resources\MoneyBoxes\Pages;

use App\Filament\Resources\MoneyBoxes\MoneyBoxResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMoneyBox extends EditRecord
{
    protected static string $resource = MoneyBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
