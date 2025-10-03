<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HouseResource\Pages;
use App\Filament\Resources\HouseResource\RelationManagers;
use App\Models\Facility;
use App\Models\House;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;

class HouseResource extends Resource
{
    protected static ?string $model = House::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Fieldset::make('Details')
                    ->schema([
                        TextInput::make('name')->maxLength(255)->required(),

                        TextInput::make('price')->required()->numeric()->prefix('IDR'),

                        Select::make('certificate')->options([
                            'SHM' => 'SHM',
                            'SHGB' => 'SHGB', 
                            'Patches' => 'Patches',
                        ]),

                        FileUpload::make('thumbnail')->required()->image(),

                        Repeater::make('photos')->relationship('photos')->schema([
                            FileUpload::make('photo')->required()->image(),
                        ]),

                        Repeater::make('facilities')->relationship('facilities')->schema([
                            Select::make('facility_id')
                            ->label('Facility')
                            ->options(Facility::all()->pluck('name','id'))->searchable()->required(),
                        ]),
                    ]),

                Fieldset::make('Additional')
                    ->schema([
                        Textarea::make('about')->required(),

                        Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('electric')
                            ->required()
                            ->numeric()
                            ->prefix('Watts'),

                        TextInput::make('land_area')
                            ->required()
                            ->numeric()
                            ->prefix('m2'),

                        TextInput::make('building_area')
                            ->required()
                            ->numeric()
                            ->prefix('m2'),

                        TextInput::make('bedroom')
                            ->required()
                            ->numeric('Unit'),

                        TextInput::make('bathroom')
                            ->required()
                            ->numeric('Unit'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                ImageColumn::make('thumbnail'),

                TextColumn::make('name')->searchable(),
                
                TextColumn::make('category.name'),

                TextColumn::make('city.name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListHouses::route('/'),
            'create' => Pages\CreateHouse::route('/create'),
            'edit' => Pages\EditHouse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
