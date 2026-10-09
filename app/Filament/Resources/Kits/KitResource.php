<?php

namespace App\Filament\Resources\Kits;

use App\Filament\Resources\Kits\Pages\ManageKits;
use App\Models\Kit;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class KitResource extends Resource
{
    protected static ?string $model = Kit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kits';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kit_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Select::make('kit_status_id')
                    ->relationship('kitStatus', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kit_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kitStatus.name')
                    ->label('Status')
                    ->sortable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageKits::route('/'),
        ];
    }
}
