<?php

namespace App\Filament\Resources\PiggyDonations\Pages;

use App\Filament\Resources\PiggyDonations\PiggyDonationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPiggyDonations extends ListRecords
{
    protected static string $resource = PiggyDonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
