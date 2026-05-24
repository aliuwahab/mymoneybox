<?php

namespace App\Filament\Resources\EventBoxes;

use App\Filament\Resources\EventBoxes\Pages\ListEventBoxes;
use App\Filament\Resources\EventBoxes\Pages\ViewEventBox;
use App\Filament\Resources\EventBoxes\Tables\EventBoxesTable;
use App\Models\EventBox;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventBoxResource extends Resource
{
    protected static ?string $model = EventBox::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|UnitEnum|null $navigationGroup = 'Events';

    protected static ?string $navigationLabel = 'EventBoxes';

    protected static ?string $modelLabel = 'EventBox';

    protected static ?string $pluralModelLabel = 'EventBoxes';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return EventBoxesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventBoxes::route('/'),
            'view'  => ViewEventBox::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}