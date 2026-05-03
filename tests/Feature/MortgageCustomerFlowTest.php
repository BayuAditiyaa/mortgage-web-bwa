<?php

namespace Tests\Feature;

use App\Models\Installment;
use App\Models\Interest;
use App\Models\MortgageRequest;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\MidtransService;
use Database\Seeders\DemoContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class MortgageCustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_approved_mortgage_and_create_payment_token(): void
    {
        Storage::fake('public');
        $this->seed(DemoContentSeeder::class);

        $user = User::factory()->create();
        $interest = Interest::with('house')->firstOrFail();

        $this->actingAs($user)
            ->get(route('front.details', $interest->house->slug))
            ->assertOk()
            ->assertSee('Quick KPR Simulator');

        $this->actingAs($user)
            ->post(route('front.interest.submitted'), [
                'dp_percentage' => 20,
                'interest_id' => $interest->id,
                'documents' => UploadedFile::fake()->create('documents.pdf', 128, 'application/pdf'),
            ])
            ->assertRedirect(route('front.request_success'));

        $mortgageRequest = MortgageRequest::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Waiting for Bank', $mortgageRequest->status);

        $mortgageRequest->update(['status' => 'Approved']);

        $this->actingAs($user)
            ->get(route('dashboard.installment.details', $mortgageRequest))
            ->assertOk()
            ->assertSee('Installment Progress')
            ->assertSee('0 / ' . ($mortgageRequest->duration * 12));

        $this->mock(MidtransService::class, function ($mock) {
            $mock->shouldReceive('createSnapToken')
                ->once()
                ->with(Mockery::type('array'))
                ->andReturn('test-snap-token');
        });

        $this->actingAs($user)
            ->postJson(route('dashboard.installment.payment_store_midtrans'), [
                'mortgage_request_id' => $mortgageRequest->id,
            ])
            ->assertOk()
            ->assertJson(['snap_token' => 'test-snap-token']);

        $this->assertSame(1, PaymentTransaction::where('mortgage_request_id', $mortgageRequest->id)->count());
    }

    public function test_dashboard_progress_reflects_paid_installments(): void
    {
        $this->seed(DemoContentSeeder::class);

        $user = User::factory()->create();
        $interest = Interest::firstOrFail();
        $mortgageRequest = MortgageRequest::create([
            'user_id' => $user->id,
            'house_id' => $interest->house_id,
            'interest_id' => $interest->id,
            'duration' => 10,
            'bank_name' => $interest->bank->name,
            'interest' => $interest->interest,
            'dp_total_amount' => 100000000,
            'dp_percentage' => 20,
            'loan_total_amount' => 400000000,
            'loan_interest_total_amount' => 532000000,
            'house_price' => 500000000,
            'monthly_amount' => 4433333,
            'status' => 'Approved',
            'documents' => 'documents/test.pdf',
        ]);

        Installment::create([
            'mortgage_request_id' => $mortgageRequest->id,
            'no_of_payment' => 1,
            'total_tax_amount' => 487666,
            'grand_total_amount' => 5820999,
            'sub_total_amount' => 4433333,
            'insurance_amount' => 900000,
            'is_paid' => true,
            'payment_type' => 'Midtrans',
            'remaining_loan_amount' => 527566667,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard.installment.details', $mortgageRequest))
            ->assertOk()
            ->assertSee('1 / 120')
            ->assertSee('1% of the tenor has been paid.');
    }
}
