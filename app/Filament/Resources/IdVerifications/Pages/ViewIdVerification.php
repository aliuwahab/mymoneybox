<?php

namespace App\Filament\Resources\IdVerifications\Pages;

use App\Filament\Resources\IdVerifications\IdVerificationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIdVerification extends ViewRecord
{
    protected static string $resource = IdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
