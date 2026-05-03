<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Interest;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\InterestResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InterestResource\RelationManagers;

class InterestResource extends Resource
{
    protected static ?string $model = Interest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Vendors';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Select::make('house_id')
                    ->relationship(
                        'house',
                        'name',
                        fn (Builder $query) => $query->when(
                            Auth::user()?->hasRole('developer') && ! Auth::user()?->hasRole('admin'),
                            fn (Builder $query) => $query->where('developer_id', Auth::id())
                        )
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('bank_id')
                    ->relationship('bank', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('interest')
                    ->required()
                    ->numeric()
                    ->prefix('%'),

                TextInput::make('duration')
                    ->required()
                    ->numeric()
                    ->numeric('Years')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('house.thumbnail'),

                TextColumn::make('house.name'),

                TextColumn::make('bank.name'),

                TextColumn::make('interest'),

                TextColumn::make('duration'),
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
            'index' => Pages\ListInterests::route('/'),
            'create' => Pages\CreateInterest::route('/create'),
            'edit' => Pages\EditInterest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(
                Auth::user()?->hasRole('developer') && ! Auth::user()?->hasRole('admin'),
                fn (Builder $query) => $query->whereHas('house', fn (Builder $query) => $query->where('developer_id', Auth::id()))
            )
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
