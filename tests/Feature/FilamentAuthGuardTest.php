<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentAuthGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_customer_session_does_not_authenticate_filament_admin_panel(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer, 'web')
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_guard_can_access_filament_panel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::firstOrCreate(['name' => 'admin']));

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk();
    }
}
