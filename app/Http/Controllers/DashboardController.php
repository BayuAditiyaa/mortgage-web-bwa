<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use Illuminate\Http\Request;
use App\Models\MortgageRequest;
use App\Services\PaymentService;
use App\Services\MortgageService;

class DashboardController extends Controller
{
    //
    protected $mortgageService;
    protected $paymentService;

    public function __construct(MortgageService $mortgageService, PaymentService $paymentService)
    {
        $this->mortgageService = $mortgageService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $mortgages = $this->mortgageService->getUserMortgages(auth()->id());
        return view('customer.mortgages.index', compact($mortgages));
    }

    public function details(MortgageRequest $mortgageRequest)
    {
        $mortgageDetails = $this->mortgageService->getMortgageDetails($mortgageRequest);
        return view('customer.mortgages.details', $mortgageDetails);
    }

    public function instalment_details(Installment $installment)
    {
        $installmentDetails = $this->mortgageService->getInstallmentDetails($installment);
        return view('customer.installment.index', compact($installmentDetails));
    }

    public function installment_payment(MortgageRequest $mortgageRequest)
    {
        $installmentPaymentDetails = $this->mortgageService->getInstallmentPaymentDetails($mortgageRequest);
        return view('customer.installment.pay_installment', $installmentPaymentDetails);
    }

    public function paymentStoreMidtrans(Request $request)
    {

        try {
            $mortgageRequest = $this->mortgageService->getMortgageRequest($request->input('mortgage_request_id'));
            $snapToken = $this->paymentService->createPayment($mortgageRequest);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment Failed' . $e->getMessage()], 500);
        }
    }

    public function paymentMidtransNotification(Request $request){
        try {
            $this->paymentService->handleNotification();

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment Failed' . $e->getMessage()], 500);
        }
    }
}
