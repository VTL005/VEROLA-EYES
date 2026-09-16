<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
