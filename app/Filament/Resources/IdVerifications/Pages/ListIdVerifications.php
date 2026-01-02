<?php

namespace App\Filament\Resources\IdVerifications\Pages;

use App\Filament\Resources\IdVerifications\IdVerificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIdVerifications extends ListRecords
{
    protected static string $resource = IdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
