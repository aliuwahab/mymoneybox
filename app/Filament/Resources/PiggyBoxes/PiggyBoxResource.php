<?php

namespace App\Filament\Resources\PiggyBoxes;

use App\Filament\Resources\PiggyBoxes\Pages\CreatePiggyBox;
use App\Filament\Resources\PiggyBoxes\Pages\EditPiggyBox;
use App\Filament\Resources\PiggyBoxes\Pages\ListPiggyBoxes;
use App\Filament\Resources\PiggyBoxes\Pages\ViewPiggyBox;
use App\Filament\Resources\PiggyBoxes\Schemas\PiggyBoxForm;
use App\Filament\Resources\PiggyBoxes\Schemas\PiggyBoxInfolist;
use App\Filament\Resources\PiggyBoxes\Tables\PiggyBoxesTable;
use App\Models\PiggyBox;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PiggyBoxResource extends Resource
{
    protected static ?string $model = PiggyBox::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift-top';

    protected static string|UnitEnum|null $navigationGroup = 'Piggy Boxes & Donations';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PiggyBoxForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PiggyBoxInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiggyBoxesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPiggyBoxes::route('/'),
            'create' => CreatePiggyBox::route('/create'),
            'view' => ViewPiggyBox::route('/{record}'),
            'edit' => EditPiggyBox::route('/{record}/edit'),
        ];
    }
}
