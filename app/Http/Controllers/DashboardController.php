<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Installment;
use App\Models\Interest;
use Illuminate\Http\Request;
use App\Models\MortgageRequest;
use App\Services\PaymentService;
use App\Services\MortgageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        $userId = Auth::id();
        $mortgages = $this->mortgageService->getUserMortgages($userId);
        return view('customer.mortgages.index', compact('mortgages'));
    }

    public function overview()
    {
        $mortgages = MortgageRequest::query()
            ->with(['house.city', 'interestModel.bank', 'installments'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $totalMortgages = $mortgages->count();
        $activeMortgages = $mortgages->whereNotIn('status', ['Rejected', 'Paid Off'])->count();
        $approvedMortgages = $mortgages->where('status', 'Approved')->count();
        $paidInstallments = $mortgages->sum(fn (MortgageRequest $mortgage) => $mortgage->installments->where('is_paid', true)->count());
        $remainingLoanAmount = $mortgages->sum(fn (MortgageRequest $mortgage) => $mortgage->remaining_loan_amount);
        $latestMortgage = $mortgages->first();

        return view('customer.dashboard.overview', compact(
            'mortgages',
            'totalMortgages',
            'activeMortgages',
            'approvedMortgages',
            'paidInstallments',
            'remainingLoanAmount',
            'latestMortgage',
        ));
    }

    public function bankInterests()
    {
        $interests = Interest::query()
            ->with(['bank', 'house.city', 'house.category'])
            ->orderBy('interest')
            ->get();

        $banks = Bank::query()
            ->whereHas('interest')
            ->withCount('interest')
            ->latest()
            ->get();

        return view('customer.dashboard.bank-interests', compact('interests', 'banks'));
    }

    public function rewards()
    {
        $mortgages = MortgageRequest::query()
            ->with('installments')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $approvedMortgages = $mortgages->where('status', 'Approved')->count();
        $paidInstallments = $mortgages->sum(fn (MortgageRequest $mortgage) => $mortgage->installments->where('is_paid', true)->count());
        $rewardPoints = ($approvedMortgages * 500) + ($paidInstallments * 100);
        $currentTier = $rewardPoints >= 2000 ? 'Priority Homeowner' : ($rewardPoints >= 500 ? 'Verified Buyer' : 'Starter Buyer');

        return view('customer.dashboard.rewards', compact(
            'mortgages',
            'approvedMortgages',
            'paidInstallments',
            'rewardPoints',
            'currentTier',
        ));
    }

    public function helpCenter()
    {
        return view('customer.dashboard.help-center');
    }

    public function support()
    {
        $mortgages = MortgageRequest::query()
            ->with('house')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(4)
            ->get();

        return view('customer.dashboard.support', compact('mortgages'));
    }

    public function settings()
    {
        $user = Auth::user();
        $profileChecks = [
            'Full name' => filled($user->name),
            'Email address' => filled($user->email),
            'Profile photo' => filled($user->photo),
            'Email verified' => filled($user->email_verified_at),
        ];

        return view('customer.dashboard.settings', compact('user', 'profileChecks'));
    }

    public function details(MortgageRequest $mortgageRequest)
    {
        $this->authorizeMortgageOwner($mortgageRequest);

        $mortgageDetails = $this->mortgageService->getMortgageDetails($mortgageRequest);
        return view('customer.mortgages.details', $mortgageDetails);
    }

    public function installment_details(Installment $installment)
    {
        $this->authorizeInstallmentOwner($installment);

        $installmentDetails = $this->mortgageService->getInstallmentDetails($installment);
        return view('customer.installments.index', compact('installmentDetails'));
    }

    public function installment_payment(MortgageRequest $mortgageRequest)
    {
        $this->authorizeMortgageOwner($mortgageRequest);
        abort_unless($mortgageRequest->status === 'Approved', 403);
        abort_if($mortgageRequest->isPaidOff(), 403);

        $installmentPaymentDetails = $this->mortgageService->getInstallmentPaymentDetails($mortgageRequest);
        return view('customer.installments.pay_installment', $installmentPaymentDetails);
    }

    public function paymentStoreMidtrans(Request $request)
    {
        try {
            $mortgageRequest = $this->mortgageService->getMortgageRequest($request->input('mortgage_request_id'));
            $snapToken = $this->paymentService->createPayment($mortgageRequest);
            return response()->json(['snap_token' => $snapToken], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Mortgage request not found.'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Midtrans payment failed.', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Payment failed. Please try again.'], 500);
        }
    }

    public function paymentMidtransNotification(Request $request){
        try {
            $this->paymentService->handleNotification();

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Midtrans notification failed.', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'Payment notification failed. Please try again.'], 500);
        }
    }

    private function authorizeMortgageOwner(MortgageRequest $mortgageRequest): void
    {
        abort_unless((int) $mortgageRequest->user_id === (int) Auth::id(), 403);
    }

    private function authorizeInstallmentOwner(Installment $installment): void
    {
        $installment->loadMissing('mortgageRequest');

        abort_unless((int) $installment->mortgageRequest?->user_id === (int) Auth::id(), 403);
    }
}
