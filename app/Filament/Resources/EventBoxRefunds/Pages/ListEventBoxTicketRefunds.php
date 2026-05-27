<?php

namespace App\Filament\Resources\EventBoxRefunds\Pages;

use App\Filament\Resources\EventBoxRefunds\EventBoxTicketRefundResource;
use Filament\Resources\Pages\ListRecords;

class ListEventBoxTicketRefunds extends ListRecords
{
    protected static string $resource = EventBoxTicketRefundResource::class;
}