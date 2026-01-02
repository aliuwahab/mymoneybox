<?php

namespace App\Filament\Resources\PiggyBoxes\Pages;

use App\Filament\Resources\PiggyBoxes\PiggyBoxResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPiggyBox extends EditRecord
{
    protected static string $resource = PiggyBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
