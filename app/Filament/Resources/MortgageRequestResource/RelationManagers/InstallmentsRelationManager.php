<?php

namespace App\Filament\Resources\MortgageRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Step::make('Installments ')
                        ->schema([

                            Forms\Components\TextInput::make('no_of_payment')
                                ->label('No of Payment')
                                ->helperText('Pembayaran cicilan ke berapa')
                                ->required()
                                ->numeric(),

                            Select::make('sub_total_amount')
                                ->label('Monthly Payment')
                                ->options(function () {
                                    $mortgageRequest = $this->getOwnerRecord();
                                    return $mortgageRequest ?
                                        [$mortgageRequest->monthly_amount => $mortgageRequest->monthly_amount]
                                        : [];
                                })
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $tax = $state * 0.11;
                                    $sub_total_amount = $state;
                                    $insurance = 900000;
                                    $grandTotal = $state + $tax + $insurance;

                                    $set('total_tax_amount', round($tax));
                                    $set('insurance_amount', $insurance);
                                    $set('grand_total_amount', round($grandTotal));

                                    $mortgageRequest = $this->getOwnerRecord();
                                    if ($mortgageRequest) {
                                        $lastInstallment = $mortgageRequest->installments()
                                            ->where('is_paid', true)
                                            ->orderBy('no_of_payment', 'desc')
                                            ->first();

                                        $previousRemainingLoan = $lastInstallment ? $lastInstallment->remaining_loan_amount : $mortgageRequest->loan_interest_total_amount;

                                        $remainingLoanAfterPayment = max($previousRemainingLoan - round($sub_total_amount), 0);

                                        $set('remaining_loan_amount', $remainingLoanAfterPayment);
                                        $set('remaining_loan_amount_before_payment', $previousRemainingLoan);
                                    }
                                }),

                            TextInput::make('total_tax_amount')
                                ->label('Tax 11%')
                                ->readOnly()
                                ->required()
                                ->numeric()
                                ->prefix('IDR'),

                            TextInput::make('insurance_amount')
                                ->label('Additional Insurance')
                                ->readOnly()
                                ->numeric()
                                ->default(900000)
                                ->prefix('IDR'),

                            TextInput::make('grand_total_amount')
                                ->label('Total Payment')
                                ->readOnly()
                                ->required()
                                ->numeric()
                                ->prefix('IDR'),

                            TextInput::make('remaining_loan_amount_before_payment')
                                ->label('Remaining Loan Amount Before Payment')
                                ->readOnly()
                                ->numeric()
                                ->prefix('IDR'),

                            TextInput::make('remaining_loan_amount')
                                ->label('Remaining Loan Amount After Payment')
                                ->readOnly()
                                ->numeric()
                                ->prefix('IDR'),
                        ]),
                    Step::make('Payment Method')
                        ->schema([
                            ToggleButtons::make('is_paid')
                                ->label('Payment Status')
                                ->boolean()
                                ->grouped()
                                ->icons([
                                    true => 'heroicon-o-check-circle',
                                    false => 'heroicon-o-x-circle',
                                ])
                                ->required(),

                            Select::make('payment_type')
                                ->label('Payment Type')
                                ->options([
                                    'Midtrans' => 'Midtrans',
                                    'Manual' => 'Manual',
                                ])
                                ->required(),


                            FileUpload::make('proof')
                                ->label('Payment Proof')
                                ->image(),
                        ]),
                ])
                    ->columnSpan('full')
                    ->columns(1)
                    ->skippable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('no_of_payment')
            ->columns([
                Tables\Columns\TextColumn::make('no_of_payment'),
                Tables\Columns\TextColumn::make('sub_total_amount'),
                Tables\Columns\TextColumn::make('insurance_amount'),
                Tables\Columns\TextColumn::make('total_tax_amount'),
                IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('verified'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
