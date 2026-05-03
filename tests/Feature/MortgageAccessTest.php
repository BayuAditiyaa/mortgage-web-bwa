<?php

namespace Tests\Feature;

use App\Models\Installment;
use App\Models\MortgageRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MortgageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_users_mortgage_details(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $mortgageRequest = $this->createMortgageRequestFor($owner);

        $this->actingAs($viewer)
            ->get(route('dashboard.installment.details', $mortgageRequest))
            ->assertForbidden();
    }

    public function test_user_cannot_open_another_users_installment_payment_page(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $mortgageRequest = $this->createMortgageRequestFor($owner);

        $this->actingAs($viewer)
            ->get(route('dashboard.installment.payment', $mortgageRequest))
            ->assertForbidden();
    }

    public function test_user_cannot_view_another_users_installment_details(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $installment = $this->createInstallmentFor($this->createMortgageRequestFor($owner));

        $this->actingAs($viewer)
            ->get(route('dashboard.mortgage.details', $installment))
            ->assertForbidden();
    }

    public function test_user_cannot_create_payment_for_another_users_mortgage(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $mortgageRequest = $this->createMortgageRequestFor($owner);

        $this->actingAs($viewer)
            ->postJson(route('dashboard.installment.payment_store_midtrans'), [
                'mortgage_request_id' => $mortgageRequest->id,
            ])
            ->assertNotFound();
    }

    private function createMortgageRequestFor(User $user): MortgageRequest
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
            'about' => 'A house used for access control tests.',
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
            'status' => 'Waiting for Bank',
            'documents' => 'documents/test.pdf',
        ]);
    }

    private function createInstallmentFor(MortgageRequest $mortgageRequest): Installment
    {
        return Installment::create([
            'mortgage_request_id' => $mortgageRequest->id,
            'no_of_payment' => 1,
            'total_tax_amount' => 487666,
            'grand_total_amount' => 5820999,
            'sub_total_amount' => 4433333,
            'insurance_amount' => 900000,
            'is_paid' => false,
            'payment_type' => 'Midtrans',
            'remaining_loan_amount' => 527566667,
        ]);
    }
}
