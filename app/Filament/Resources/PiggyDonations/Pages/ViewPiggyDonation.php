<?php

namespace App\Filament\Resources\PiggyDonations\Pages;

use App\Filament\Resources\PiggyDonations\PiggyDonationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPiggyDonation extends ViewRecord
{
    protected static string $resource = PiggyDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
