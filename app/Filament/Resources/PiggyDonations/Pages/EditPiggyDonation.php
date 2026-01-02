<?php

namespace App\Filament\Resources\PiggyDonations\Pages;

use App\Filament\Resources\PiggyDonations\PiggyDonationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPiggyDonation extends EditRecord
{
    protected static string $resource = PiggyDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
