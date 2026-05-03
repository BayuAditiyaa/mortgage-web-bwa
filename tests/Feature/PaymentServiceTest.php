<?php

namespace Tests\Feature;

use App\Models\Installment;
use App\Models\MortgageRequest;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\MidtransService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_notification_is_idempotent(): void
    {
        $mortgageRequest = $this->createApprovedMortgageRequestFor(User::factory()->create());

        $midtrans = Mockery::mock(MidtransService::class);
        $midtrans->shouldReceive('handleNotification')
            ->twice()
            ->andReturn([
                'order_id' => 'KPR-TEST-ORDER',
                'transaction_status' => 'settlement',
                'gross_amount' => '5820999',
                'custom_field1' => (string) $mortgageRequest->user_id,
                'custom_field2' => (string) $mortgageRequest->id,
            ]);

        $service = new PaymentService($midtrans);
        $service->handleNotification();
        $service->handleNotification();

        $this->assertSame(1, Installment::where('mortgage_request_id', $mortgageRequest->id)->count());
        $this->assertSame(1, PaymentTransaction::where('order_id', 'KPR-TEST-ORDER')->count());
        $this->assertNotNull(PaymentTransaction::where('order_id', 'KPR-TEST-ORDER')->first()->installment_id);
    }

    private function createApprovedMortgageRequestFor(User $user): MortgageRequest
    {
        $timestamp = now();

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'House',
            'slug' => 'house',
            'photo' => 'category.jpg',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $cityId = DB::table('cities')->insertGetId([
            'name' => 'Jakarta',
            'slug' => 'jakarta',
            'photo' => 'city.jpg',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $houseId = DB::table('houses')->insertGetId([
            'name' => 'Test House',
            'slug' => 'test-house',
            'thumbnail' => 'house.jpg',
            'certificate' => 'SHM',
            'about' => 'A house used for payment tests.',
            'price' => 500000000,
            'bedroom' => 3,
            'bathroom' => 2,
            'electric' => 2200,
            'land_area' => 90,
            'building_area' => 120,
            'category_id' => $categoryId,
            'city_id' => $cityId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $bankId = DB::table('banks')->insertGetId([
            'name' => 'Test Bank',
            'photo' => 'bank.jpg',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $interestId = DB::table('interests')->insertGetId([
            'house_id' => $houseId,
            'bank_id' => $bankId,
            'interest' => 6,
            'duration' => 10,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return MortgageRequest::create([
            'user_id' => $user->id,
            'house_id' => $houseId,
            'interest_id' => $interestId,
            'duration' => 10,
            'bank_name' => 'Test Bank',
            'interest' => 6,
            'dp_total_amount' => 100000000,
            'dp_percentage' => 20,
            'loan_total_amount' => 400000000,
            'loan_interest_total_amount' => 532000000,
            'house_price' => 500000000,
            'monthly_amount' => 4433333,
            'status' => 'Approved',
            'documents' => 'documents/test.pdf',
        ]);
    }
}
