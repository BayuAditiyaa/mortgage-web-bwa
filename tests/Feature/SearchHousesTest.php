<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchHousesTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_can_render_without_filters(): void
    {
        $this->get(route('front.search'))->assertOk();
    }
}
