<?php

namespace App\Filament\Resources\PiggyDonations;

use App\Filament\Resources\PiggyDonations\Pages\CreatePiggyDonation;
use App\Filament\Resources\PiggyDonations\Pages\EditPiggyDonation;
use App\Filament\Resources\PiggyDonations\Pages\ListPiggyDonations;
use App\Filament\Resources\PiggyDonations\Pages\ViewPiggyDonation;
use App\Filament\Resources\PiggyDonations\Schemas\PiggyDonationForm;
use App\Filament\Resources\PiggyDonations\Schemas\PiggyDonationInfolist;
use App\Filament\Resources\PiggyDonations\Tables\PiggyDonationsTable;
use App\Models\PiggyDonation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PiggyDonationResource extends Resource
{
    protected static ?string $model = PiggyDonation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|UnitEnum|null $navigationGroup = 'Piggy Boxes & Donations';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'donor_name';

    public static function form(Schema $schema): Schema
    {
        return PiggyDonationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PiggyDonationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PiggyDonationsTable::configure($table);
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
            'index' => ListPiggyDonations::route('/'),
            'create' => CreatePiggyDonation::route('/create'),
            'view' => ViewPiggyDonation::route('/{record}'),
            'edit' => EditPiggyDonation::route('/{record}/edit'),
        ];
    }
}
