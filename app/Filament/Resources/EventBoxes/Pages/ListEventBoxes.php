<?php

namespace App\Filament\Resources\EventBoxes\Pages;

use App\Filament\Resources\EventBoxes\EventBoxResource;
use Filament\Resources\Pages\ListRecords;

class ListEventBoxes extends ListRecords
{
    protected static string $resource = EventBoxResource::class;
}