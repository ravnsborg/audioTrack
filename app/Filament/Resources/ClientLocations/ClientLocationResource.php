<?php

namespace App\Filament\Resources\ClientLocations;

use App\Filament\Resources\ClientLocations\Pages\CreateClientLocation;
use App\Filament\Resources\ClientLocations\Pages\EditClientLocation;
use App\Filament\Resources\ClientLocations\Pages\ListClientLocations;
use App\Filament\Resources\ClientLocations\Schemas\ClientLocationForm;
use App\Filament\Resources\ClientLocations\Tables\ClientLocationsTable;
use App\Models\ClientLocation;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientLocationResource extends Resource
{
    protected static ?string $model = ClientLocation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Client Locations';

    protected static ?int $navigationSort = 3;

    public static function getNavigationItems(): array
    {
        return array_map(
            fn (NavigationItem $item): NavigationItem => $item->extraAttributes([
                'style' => 'border-top: 1px solid rgba(156, 163, 175, 0.35); margin-top: 0.5rem; padding-top: 0.5rem;',
            ]),
            parent::getNavigationItems(),
        );
    }

    public static function form(Schema $schema): Schema
    {
        return ClientLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientLocationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientLocations::route('/'),
            'create' => CreateClientLocation::route('/create'),
            'edit' => EditClientLocation::route('/{record}/edit'),
        ];
    }
}
