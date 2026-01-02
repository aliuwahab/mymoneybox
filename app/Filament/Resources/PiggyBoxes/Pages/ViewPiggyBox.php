<?php

namespace App\Filament\Resources\PiggyBoxes\Pages;

use App\Filament\Resources\PiggyBoxes\PiggyBoxResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPiggyBox extends ViewRecord
{
    protected static string $resource = PiggyBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
