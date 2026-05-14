<?php

namespace App\Filament\Resources\TrustedLogos\Pages;

use App\Filament\Resources\TrustedLogos\TrustedLogoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrustedLogo extends EditRecord
{
    protected static string $resource = TrustedLogoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
