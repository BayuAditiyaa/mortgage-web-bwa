<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;



Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');
Route::get('/details/{house:slug}', [FrontController::class, 'details'])->name('front.details');
Route::get('/search', [FrontController::class, 'search'])->name('front.search');

  
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard/mortgage/{mortgageRequest}/installment/payment', [DashboardController::class, 'installment_payment'])->name('dashboard.installment.payment');

    Route::post('/dashboard/mortgage/installment/payment', [DashboardController::class, 'paymentStoreMidtrans'])->name('dashboard.installment.payment_store_ midtrans');

    Route::get('/request/mortgage/{interest}', [FrontController::class, 'interest'])->name('front.interest');

    Route::get('/request/mortgage/submitted', [FrontController::class, 'request_interest'])->name('front.interest.submitted'); 

    Route::get('/request/success', [FrontController::class, 'request_success'])->name('front.request_success'); 

    Route::get('/dashboard/mortgages/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard/mortgages/{mortgageRequest}', [DashboardController::class, 'details'])->name('dashboard.mortgage.details');

    
    Route::get('/dashboard/mortgages/installment/{installment}', [DashboardController::class, 'installment'])->name('dashboard.installment.details');


});

require __DIR__.'/auth.php';