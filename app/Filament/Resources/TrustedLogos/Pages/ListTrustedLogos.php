<?php

namespace App\Filament\Resources\TrustedLogos\Pages;

use App\Filament\Resources\TrustedLogos\TrustedLogoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrustedLogos extends ListRecords
{
    protected static string $resource = TrustedLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
