<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\MortgageRequest;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function createPayment(MortgageRequest $mortgageRequest)
    {
        if ($mortgageRequest->status !== 'Approved') {
            throw ValidationException::withMessages([
                'mortgage_request_id' => 'Mortgage request must be approved before payment.',
            ]);
        }

        if ($mortgageRequest->isPaidOff()) {
            throw ValidationException::withMessages([
                'mortgage_request_id' => 'This mortgage has already been paid off.',
            ]);
        }

        $pendingTransaction = $mortgageRequest->paymentTransactions()
            ->where('transaction_status', 'pending')
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if ($pendingTransaction) {
            return $pendingTransaction->snap_token;
        }

        $sub_total_amount = $mortgageRequest->monthly_amount;
        $insurance = 900000;
        $total_tax_amount = round($sub_total_amount * 0.11);

        $grossAmount = round($sub_total_amount + $insurance + $total_tax_amount);
        $orderId = 'KPR-' . $mortgageRequest->id . '-' . now()->format('YmdHis') . '-' . substr(uniqid(), -6);

        $transaction = $mortgageRequest->paymentTransactions()->create([
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone,
            ],
            'item_details' => [
                [
                    'id' => $mortgageRequest->id,
                    'price' => $grossAmount,
                    'quantity' => 1,
                    'name' => 'Pembayaran cicilan rumah' . $mortgageRequest->house->name,
                ]
            ],
            'custom_field1' => auth()->id(),
            'custom_field2' => $mortgageRequest->id,
        ];

        $snapToken = $this->midtransService->createSnapToken($params);

        $transaction->update(['snap_token' => $snapToken]);

        return $snapToken;
    }

    public function handleNotification()
    {
        $notification = $this->midtransService->handleNotification();

        $transactionStatus = $notification['transaction_status'];
        $grossAmount = $notification['gross_amount'];
        $orderId = $notification['order_id'];

        $paymentTransaction = PaymentTransaction::firstOrCreate(
            ['order_id' => $orderId],
            [
                'mortgage_request_id' => $notification['custom_field2'],
                'gross_amount' => round((float) $grossAmount),
                'transaction_status' => 'pending',
            ]
        );

        if ($paymentTransaction->installment_id) {
            return;
        }

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            $mortgageRequestId = $notification['custom_field2'];

            DB::transaction(function () use ($paymentTransaction, $mortgageRequestId, $grossAmount, $transactionStatus, $notification) {
                $mortgageRequest = MortgageRequest::lockForUpdate()->findOrFail($mortgageRequestId);
                $lockedPaymentTransaction = PaymentTransaction::whereKey($paymentTransaction->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedPaymentTransaction->installment_id) {
                    return;
                }

                $installment = $this->createInstallment($mortgageRequest, $grossAmount);

                $lockedPaymentTransaction->update([
                    'installment_id' => $installment->id,
                    'transaction_status' => $transactionStatus,
                    'gross_amount' => round((float) $grossAmount),
                    'payload' => $notification,
                    'paid_at' => now(),
                ]);

                if ($mortgageRequest->fresh()->isPaidOff()) {
                    $mortgageRequest->update(['status' => 'Paid Off']);
                }
            });
        } else {
            $paymentTransaction->update([
                'transaction_status' => $transactionStatus,
                'payload' => $notification,
            ]);
        }
    }

    private function createInstallment(MortgageRequest $mortgageRequest, $grossAmount)
    {
        if ($mortgageRequest->isPaidOff()) {
            throw ValidationException::withMessages([
                'mortgage_request_id' => 'This mortgage has already been paid off.',
            ]);
        }

        $lastInstallment = $mortgageRequest->installments()
            ->where('is_paid', true)
            ->orderBy('no_of_payment', 'desc')
            ->first();

        $previousRemainingLoan = $lastInstallment ? $lastInstallment->remaining_loan_amount : $mortgageRequest->loan_interest_total_amount;

        $sub_total_amount = $mortgageRequest->monthly_amount;
        $insurance = 900000;
        $total_tax_amount = round($sub_total_amount * 0.11);

        $remainingLoan = max($previousRemainingLoan - $sub_total_amount, 0);

        return Installment::create([
            'mortgage_request_id' => $mortgageRequest->id,
            'no_of_payment' => $mortgageRequest->installments()->where('is_paid', true)->count() + 1,
            'total_tax_amount' => $total_tax_amount,
            'grand_total_amount' => $grossAmount,
            'sub_total_amount' => $sub_total_amount,
            'insurance_amount' => $insurance,
            'is_paid' => true,
            'payment_type' => 'Midtrans',
            'remaining_loan_amount' => $remainingLoan,
        ]);
    }
}
