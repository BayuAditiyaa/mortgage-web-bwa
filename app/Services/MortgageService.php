<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Interest;
use App\Models\MortgageRequest;
use Illuminate\Support\Facades\Auth;

class MortgageService
{
    //

    public function handleInterestRequest($request){
        $validatedData = $request->validate([
            'dp_percentage' => 'required|integer|min:0|max:100',
            'interest_id' => 'required|integer|exists:interests,id',
            'documents' => 'required|file|mimes:pdf|max:2048'
        ]);

        $interest = Interest::findOrfail($validatedData['interest_id']);
        $house = $interest->house();

        $mortgageDetails = $this->calculateMortgageDetails($house, $interest, $validatedData['dp_percentage']);

        $documentPath = $this->uploadDocument($request);

        return $this->createMortgageRequest($mortgageDetails, $documentPath);
    }

    public function calculateMortgageDetails($house, $interest, $dpPercentage) {
        $housePrice = $house->price;
        $dpTotalAmount = $housePrice * ($dpPercentage / 100);
        $loanTotalAmount = $housePrice - $dpTotalAmount;
        $durationYears = $interest->duration;
        $totalPayments = $durationYears * 12;
        $monthlyInterestRate = $interest->interest / 100 / 12;

        //Amortization formula for monthly payments
        $numerator = $loanTotalAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalPayments);
        $denominator = pow(1 + $monthlyInterestRate, $totalPayments) - 1;
        $monthlyAmount = $denominator > 0 ? $numerator / $denominator : 0;

        $loanInterestTotalAmount = $monthlyAmount * $totalPayments;

        return compact(
            'house',
            'interest',
            'housePrice',
            'dpTotalAmount',
            'dpPercentage',
            'loanTotalAmount',
            'monthlyAmount',
            'loanInterestTotalAmount',
        );
        

    }

    public function uploadDocument($request)
    {
        if ($request->hasFile('document')) {
            return $request->file('documents')->store('document', 'public');
        }
        return null;
    }

    public function createMortgageRequest($details, $documentPath)
    {
        $mortgageRequest = MortgageRequest::create([
            'user_id' => Auth::id(),
            'house_id' => $details['house']->id,
            'interest_id' => $details['interest']->id,
            'interest' => $details['interest']->interest,
            'duration' => $details['interest']->duration,
            'dp_percentage' => $details['dpPercentage'],
            'house_price' => $details['housePrice'],
            'dp_total_amount' => $details['dpTotalAmount'],
            'loan_total_amount' => $details['loanTotalAmount'],
            'monthly_amount' => $details['monthlyAmount'],
            'status' => 'Waiting for Bank',
            'documents' => $documentPath,

        ]);

        session(['interest_id' => $details['interest']->id]);

        return $mortgageRequest;
    }

    public function getInterestFromSession()
    {
        return Interest::findOrFail(session('interest_id')) ?? null;
    }

    public function getUserMortgages($userId){
        return MortgageRequest::with(['house', 'house.city', 'house.category'])->where('user_id', $userId)->get();
    }

    public function getMortgageDetails( MortgageRequest $mortgageRequest){
        $mortgageRequest->load(['house.city', 'house.category', 'installments']);
        $monthlyPayment = $mortgageRequest->monthly_amount;
        $insurance = 900000;
        $totalTaxAmount = round($monthlyPayment * 0.11);

        return compact(
            'mortgageRequest',
            'insurance',
            'totalTaxAmount',
        );
    }

    public function getInstallmentDetails(Installment $installment){
        return $installment->load(['mortgageRequest.house.city']);
    }

    public function getInstallmentPaymentDetails(MortgageRequest $mortgageRequest){
        $remainingLoanAmount = $mortgageRequest->remaining_loan_amount;
        $mortgageRequest->load(['house.city', 'house.category']);
        $monthlyPayment = $mortgageRequest->monthly_amount;
        $insurance = 900000;
        $totalTaxAmount = round($monthlyPayment * 0.11);
        $grandTotalAmount = $monthlyPayment + $insurance + $totalTaxAmount;
        $remainingLoanAmountAfterPayment = $remainingLoanAmount - $monthlyPayment;

        return compact(
            'mortgageRequest',
            'grandTotalAmount',
            'monthlyPayment', 
            'totalTaxAmount',
            'insurance',
            'remainingLoanAmount',
            'remainingLoanAmountAfterPayment',
        );
    } 

    public function getMortgageRequest($mortgageRequestId){
        return MortgageRequest::findOrFail($mortgageRequestId);    }
}
