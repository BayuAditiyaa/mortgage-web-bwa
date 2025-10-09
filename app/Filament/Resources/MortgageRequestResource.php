<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\House;
use App\Models\Interest;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MortgageRequest;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MortgageRequestResource\Pages;
use App\Filament\Resources\MortgageRequestResource\RelationManagers;
use App\Filament\Resources\MortgageRequestResource\RelationManagers\InstallmentsRelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class MortgageRequestResource extends Resource
{
    protected static ?string $model = MortgageRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Wizard::make([
                    Step::make('Product and Price')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Select::make('house_id')
                                        ->label('House')
                                        ->options(House::query()->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $house = House::find($state);
                                            if ($house) {
                                                $set('house_price', $house->price ?? 0);
                                            }
                                        }),

                                    Select::make('interest_id')
                                        ->label('Annual  Interest in %')
                                        ->options(function (callable $get) {
                                            $houseId = $get('house_id');
                                            if ($houseId) {
                                                return Interest::where('house_id', $houseId)
                                                    ->get()
                                                    ->pluck('interest', 'id' );
                                            }
                                            return [];
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $interest = Interest::find($state);
                                            if ($interest) {
                                                $set('bank_name', $interest->bank->name ?? '');
                                                $set('interest', $interest->interest);
                                                $set('duration', $interest->duration);
                                            }
                                        }),

                                    TextInput::make('bank_name')
                                        ->label('Bank Name')
                                        ->required()
                                        ->readOnly(),

                                    TextInput::make('duration')
                                        ->label('Duration in Years')
                                        ->required()
                                        ->readOnly()
                                        ->numeric()
                                        ->suffix('Years'),

                                    TextInput::make('interest')
                                        ->label('Interest Rate')
                                        ->required()
                                        ->readOnly()
                                        ->numeric()
                                        ->suffix('%'),

                                    TextInput::make('house_price')
                                        ->label('House Price')
                                        ->required()
                                        ->readOnly()
                                        ->numeric()
                                        ->prefix('IDR'),

                                    Select::make('dp_percentage')
                                        ->label('Down Payment (%)')
                                        ->options([
                                            '10' => '10%',
                                            '20' => '20%',
                                            '40' => '40%',
                                            '50' => '50%',
                                        ])
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $housePrice = $get('house_price') ?? 0;
                                            $dpAmount = ($state / 100) * $housePrice;
                                            $loanAmount = max($housePrice - $dpAmount, 0);

                                            $set('dp_total_amount', round($dpAmount));
                                            $set('loan_total_amount', round($loanAmount));

                                            //calculate monthly payment
                                            $durationYears = $get('duration') ?? 0;
                                            $interestRate = $get('interest') ?? 0;

                                            if ($durationYears > 0 && $interestRate > 0 && $loanAmount > 0) {
                                                $totalPayments = $durationYears * 12;
                                                $monthlyInterestRate = $interestRate / 100 / 12;

                                                //amortization formula
                                                $numerator = $loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalPayments);
                                                $denominator = pow(1 + $monthlyInterestRate, $totalPayments) - 1;
                                                $monthlyPayment = $denominator > 0 ? $numerator / $denominator : 0;

                                                $set('monthly_amount', round($monthlyPayment));

                                                //Total loan with interest
                                                $loanInteresTotalAmount = $monthlyPayment * $totalPayments;
                                                $set('loan_interest_total_amount', round($loanInteresTotalAmount));
                                            } else {
                                                $set('monthly_amount', 0);
                                                $set('loan_interest_total_amount', 0);
                                            }
                                        }),

                                    TextInput::make('dp_total_amount')
                                        ->label('Down Payment Amount')
                                        ->readOnly()
                                        ->numeric()
                                        ->prefix('IDR'),

                                    TextInput::make('loan_total_amount')
                                        ->label('Loan Amount')
                                        ->required()
                                        ->readOnly()
                                        ->numeric()
                                        ->prefix('IDR'),

                                    TextInput::make('monthly_amount')
                                        ->label('Monthly Payment')
                                        ->required()
                                        ->readOnly()
                                        ->numeric()
                                        ->prefix('IDR'),

                                    TextInput::make('loan_interest_total_amount')
                                        ->label('Total Payment Amount')
                                        ->readOnly()
                                        ->numeric()
                                        ->prefix('IDR'),
                                ]),
                        ]),
                    Step::make('Customer Information')
                        ->schema([
                            Select::make('user_id')
                                ->relationship('customer', 'email')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $user = User::find($state);

                                    $name = $user->name;
                                    $email = $user->email;

                                    $set('name', $name);
                                    $set('email', $email);
                                })
                                ->afterStateHydrated(function ($state, callable $set) {
                                    $userId = $state;
                                    if ($userId) {
                                        $user = User::find($userId);
                                        $name = $user->name;
                                        $email = $user->email;

                                        $set('name', $name);
                                        $set('email', $email);
                                    }
                                }),

                            TextInput::make('name')
                                ->required()
                                ->readOnly()
                                ->maxLength('255'),

                            TextInput::make('email')
                                ->required()
                                ->readOnly()
                                ->maxLength('255'),
                            ]),
                    Step::make('bank Approval')
                        ->schema([
                            FileUpload::make('documents')
                            ->acceptedFileTypes(['application/pdf'])
                            ->required(),

                            Select::make('status')
                            ->label('Approval Status')
                            ->options([
                                'Waiting for Bank' => 'Waiting for Bank',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->required(),
                        ])

                ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->skippable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                ImageColumn::make('house.thumbnail'),

                TextColumn::make('customer.name')
                    ->searchable(),

                TextColumn::make('house.name'),
                TextColumn::make('status'),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),

                Action::make('download')
                ->label('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn(MortgageRequest $record) => asset('storage/' . $record->documents))
                ->openUrlInNewTab(),
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
            InstallmentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMortgageRequests::route('/'),
            'create' => Pages\CreateMortgageRequest::route('/create'),
            'edit' => Pages\EditMortgageRequest::route('/{record}/edit'),
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
