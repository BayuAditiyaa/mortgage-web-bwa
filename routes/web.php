<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');
Route::get('/details/{house:slug}', [FrontController::class, 'details'])->name('front.details');
Route::get('/search', [FrontController::class, 'search'])->name('front.search');
Route::get('/browse', [FrontController::class, 'browse'])->name('front.browse');
Route::get('/rewards', [FrontController::class, 'rewards'])->name('front.rewards');
Route::get('/stories', [FrontController::class, 'stories'])->name('front.stories');

Route::match(['get', 'post'], '/mortgage/interest/payment/midtrans/notification', [DashboardController::class, 'paymentMidtransNotification'])->name('front.payment_midtrans_notification');

  
Route::get('/dashboard', function () {
    return redirect()->route('dashboard.mortgages.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard/mortgage/{mortgageRequest}/installment/payment', [DashboardController::class, 'installment_payment'])->name('dashboard.installment.payment');

    Route::post('/dashboard/mortgage/installment/payment', [DashboardController::class, 'paymentStoreMidtrans'])->name('dashboard.installment.payment_store_midtrans');

    Route::get('/request/mortgage/{interest}', [FrontController::class, 'interest'])->name('front.interest');

    Route::post('/request/mortgage/submitted', [FrontController::class, 'request_interest'])->name('front.interest.submitted'); 

    Route::get('/request/success', [FrontController::class, 'request_success'])->name('front.request_success'); 

    Route::get('/dashboard/mortgages', [DashboardController::class, 'index'])->name('dashboard.mortgages.index');
    Route::get('/dashboard/overview', [DashboardController::class, 'overview'])->name('dashboard.overview');
    Route::get('/dashboard/bank-interests', [DashboardController::class, 'bankInterests'])->name('dashboard.bank-interests');
    Route::get('/dashboard/rewards', [DashboardController::class, 'rewards'])->name('dashboard.rewards');
    Route::get('/dashboard/help-center', [DashboardController::class, 'helpCenter'])->name('dashboard.help-center');
    Route::get('/dashboard/support', [DashboardController::class, 'support'])->name('dashboard.support');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    
    Route::get('/dashboard/mortgage/{mortgageRequest}', [DashboardController::class, 'details'])->name('dashboard.installment.details');

    Route::get('/dashboard/mortgages/installment/{installment}', [DashboardController::class, 'installment_details'])->name('dashboard.mortgage.details');


});

require __DIR__.'/auth.php';
