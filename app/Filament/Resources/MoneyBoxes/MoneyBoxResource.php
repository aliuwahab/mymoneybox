<?php

namespace App\Filament\Resources\MoneyBoxes;

use App\Filament\Resources\MoneyBoxes\Pages\CreateMoneyBox;
use App\Filament\Resources\MoneyBoxes\Pages\EditMoneyBox;
use App\Filament\Resources\MoneyBoxes\Pages\ListMoneyBoxes;
use App\Filament\Resources\MoneyBoxes\Pages\ViewMoneyBox;
use App\Filament\Resources\MoneyBoxes\Schemas\MoneyBoxForm;
use App\Filament\Resources\MoneyBoxes\Schemas\MoneyBoxInfolist;
use App\Filament\Resources\MoneyBoxes\Tables\MoneyBoxesTable;
use App\Models\MoneyBox;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MoneyBoxResource extends Resource
{
    protected static ?string $model = MoneyBox::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|UnitEnum|null $navigationGroup = 'Money Boxes & Contributions';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return MoneyBoxForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MoneyBoxInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoneyBoxesTable::configure($table);
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
            'index' => ListMoneyBoxes::route('/'),
            'create' => CreateMoneyBox::route('/create'),
            'view' => ViewMoneyBox::route('/{record}'),
            'edit' => EditMoneyBox::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
