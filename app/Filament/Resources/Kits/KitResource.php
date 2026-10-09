<?php

namespace App\Filament\Resources\Kits;

use App\Filament\Resources\Kits\Pages\ManageKits;
use App\Models\Item;
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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class KitResource extends Resource
{
    protected static ?string $model = Kit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $navigationLabel = 'Kits';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('items:id,kit_id,name,sku');
    }

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
                    ->label('Items')
                    ->tooltip(fn (Kit $record): HtmlString => new HtmlString(
                        '<div style="min-width: 14rem;"><h3 style="font-weight: 600; margin: 0;">Assigned Items</h3>'.
                        '<hr style="border: 0; border-top: 1px solid currentColor; margin: 0.5rem 0;">'.
                        ($record->items->isEmpty()
                            ? '<span>No items in this kit.</span>'
                            : '<ul style="list-style: none; padding: 0; margin: 0;">'.$record->items
                                ->map(fn (Item $item): string => '<li>&bull; '.e($item->name).' <span class="text-gray-500">('.e($item->sku).')</span></li>')
                                ->implode('').'</ul>').
                        '</div>',
                    )),
            ])
            ->filters([
                SelectFilter::make('kit_status_id')
                    ->label('Status')
                    ->relationship('kitStatus', 'name')
                    ->placeholder('All Statuses')
                    ->preload(),
            ], FiltersLayout::Hidden)
            ->deferFilters(false)
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
