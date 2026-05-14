<?php

namespace App\Filament\Resources\TrustedLogos;

use App\Filament\Resources\TrustedLogos\Pages\CreateTrustedLogo;
use App\Filament\Resources\TrustedLogos\Pages\EditTrustedLogo;
use App\Filament\Resources\TrustedLogos\Pages\ListTrustedLogos;
use App\Filament\Resources\TrustedLogos\Schemas\TrustedLogoForm;
use App\Filament\Resources\TrustedLogos\Tables\TrustedLogosTable;
use App\Models\TrustedLogo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class TrustedLogoResource extends Resource
{
    protected static ?string $model = TrustedLogo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Trusted Logos';

    protected static ?string $modelLabel = 'Trusted Logo';

    protected static ?string $pluralModelLabel = 'Trusted Logos';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TrustedLogoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrustedLogosTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrustedLogos::route('/'),
            'create' => CreateTrustedLogo::route('/create'),
            'edit' => EditTrustedLogo::route('/{record}/edit'),
        ];
    }
}
