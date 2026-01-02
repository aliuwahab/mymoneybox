<?php

namespace App\Filament\Resources\PiggyBoxes\Pages;

use App\Filament\Resources\PiggyBoxes\PiggyBoxResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiggyBoxes extends ListRecords
{
    protected static string $resource = PiggyBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
