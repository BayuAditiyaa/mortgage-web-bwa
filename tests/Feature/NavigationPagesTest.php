<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_navigation_pages_can_be_rendered(): void
    {
        $this->get(route('front.browse'))
            ->assertOk()
            ->assertSee('Mortgage Ready Catalog');

        $this->get(route('front.rewards'))
            ->assertOk()
            ->assertSee('Customer Benefits');

        $this->get(route('front.stories'))
            ->assertOk()
            ->assertSee('Case Studies');
    }

    public function test_customer_dashboard_support_pages_require_authentication(): void
    {
        $this->get(route('dashboard.overview'))->assertRedirect(route('login'));
        $this->get(route('dashboard.bank-interests'))->assertRedirect(route('login'));
        $this->get(route('dashboard.rewards'))->assertRedirect(route('login'));
        $this->get(route('dashboard.help-center'))->assertRedirect(route('login'));
        $this->get(route('dashboard.support'))->assertRedirect(route('login'));
        $this->get(route('dashboard.settings'))->assertRedirect(route('login'));
    }

    public function test_authenticated_customer_dashboard_support_pages_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard.overview'))
            ->assertOk()
            ->assertSee('Mortgage health');

        $this->actingAs($user)->get(route('dashboard.bank-interests'))
            ->assertOk()
            ->assertSee('Bank Comparison');

        $this->actingAs($user)->get(route('dashboard.rewards'))
            ->assertOk()
            ->assertSee('Reward Balance');

        $this->actingAs($user)->get(route('dashboard.help-center'))
            ->assertOk()
            ->assertSee('Frequently asked questions');

        $this->actingAs($user)->get(route('dashboard.support'))
            ->assertOk()
            ->assertSee('Contact Options');

        $this->actingAs($user)->get(route('dashboard.settings'))
            ->assertOk()
            ->assertSee('Profile Readiness');
    }
}
