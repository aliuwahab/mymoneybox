<?php

namespace App\Filament\Resources\EventBoxRefunds;

use App\Filament\Resources\EventBoxRefunds\Pages\ListEventBoxTicketRefunds;
use App\Filament\Resources\EventBoxRefunds\Pages\ViewEventBoxTicketRefund;
use App\Filament\Resources\EventBoxRefunds\Schemas\EventBoxTicketRefundInfolist;
use App\Filament\Resources\EventBoxRefunds\Tables\EventBoxTicketRefundsTable;
use App\Models\EventBoxTicketRefund;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventBoxTicketRefundResource extends Resource
{
    protected static ?string $model = EventBoxTicketRefund::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-refund';

    protected static string|UnitEnum|null $navigationGroup = 'Events';

    protected static ?string $navigationLabel = 'Ticket Refunds';

    protected static ?string $modelLabel = 'Ticket Refund';

    protected static ?string $pluralModelLabel = 'Ticket Refunds';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function infolist(Schema $schema): Schema
    {
        return EventBoxTicketRefundInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventBoxTicketRefundsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventBoxTicketRefunds::route('/'),
            'view'  => ViewEventBoxTicketRefund::route('/{record}'),
        ];
    }
}