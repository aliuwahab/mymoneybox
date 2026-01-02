<?php

namespace App\Filament\Resources\IdVerifications\Pages;

use App\Filament\Resources\IdVerifications\IdVerificationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIdVerification extends EditRecord
{
    protected static string $resource = IdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
