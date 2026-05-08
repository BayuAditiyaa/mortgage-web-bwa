<?php

namespace Tests\Feature;

use App\Filament\Resources\HouseResource;
use App\Filament\Resources\MortgageRequestResource;
use App\Models\House;
use App\Models\MortgageRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeveloperFilamentScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_only_sees_their_own_houses_in_filament_query(): void
    {
        [$developer, $otherDeveloper] = $this->createDevelopers();
        $ownHouse = $this->createHouseFor($developer, 'own-house');
        $this->createHouseFor($otherDeveloper, 'other-house');

        $this->actingAs($developer, 'admin');

        $houses = HouseResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($houses->contains($ownHouse->id));
        $this->assertCount(1, $houses);
    }

    public function test_admin_can_see_all_houses_in_filament_query(): void
    {
        [$developer, $otherDeveloper] = $this->createDevelopers();
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

        $this->createHouseFor($developer, 'own-house');
        $this->createHouseFor($otherDeveloper, 'other-house');

        $this->actingAs($admin, 'admin');

        $this->assertCount(2, HouseResource::getEloquentQuery()->pluck('id'));
    }

    public function test_developer_only_sees_mortgage_requests_for_their_houses(): void
    {
        [$developer, $otherDeveloper] = $this->createDevelopers();
        $ownMortgage = $this->createMortgageRequestFor($this->createHouseFor($developer, 'own-house'));
        $this->createMortgageRequestFor($this->createHouseFor($otherDeveloper, 'other-house'));

        $this->actingAs($developer, 'admin');

        $mortgages = MortgageRequestResource::getEloquentQuery()->pluck('id');

        $this->assertTrue($mortgages->contains($ownMortgage->id));
        $this->assertCount(1, $mortgages);
    }

    private function createDevelopers(): array
    {
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);

        $developer = User::factory()->create();
        $otherDeveloper = User::factory()->create();
        $developer->assignRole($developerRole);
        $otherDeveloper->assignRole($developerRole);

        return [$developer, $otherDeveloper];
    }

    private function createHouseFor(User $developer, string $slug): House
    {
        $timestamp = now();

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'House '.$slug,
            'slug' => 'house-'.$slug,
            'photo' => 'category.jpg',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $cityId = DB::table('cities')->insertGetId([
            'name' => 'City '.$slug,
            'slug' => 'city-'.$slug,
            'photo' => 'city.jpg',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return House::create([
            'developer_id' => $developer->id,
            'name' => 'Test House '.$slug,
            'thumbnail' => 'house.jpg',
            'certificate' => 'SHM',
            'about' => 'A house used for developer scoping tests.',
            'price' => 500000000,
            'bedroom' => 3,
            'bathroom' => 2,
            'electric' => 2200,
            'land_area' => 90,
            'building_area' => 120,
            'category_id' => $categoryId,
            'city_id' => $cityId,
        ]);
    }

    private function createMortgageRequestFor(House $house): MortgageRequest
    {
        $timestamp = now();
        $customer = User::factory()->create();

        $bankId = DB::table('banks')->insertGetId([
            'name' => 'Test Bank '.$house->id,
            'photo' => 'bank.jpg',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        $interestId = DB::table('interests')->insertGetId([
            'house_id' => $house->id,
            'bank_id' => $bankId,
            'interest' => 6,
            'duration' => 10,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return MortgageRequest::create([
            'user_id' => $customer->id,
            'house_id' => $house->id,
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
}
