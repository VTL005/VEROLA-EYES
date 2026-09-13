<?php

namespace Tests\Feature;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleAndTimezoneArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_customer_and_admin_roles_are_seeded(): void
    {
        $this->seed(RoleSeeder::class);

        $this->assertDatabaseCount('roles', 2);
        $this->assertDatabaseHas('roles', ['name' => 'customer']);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseMissing('roles', ['name' => 'staff']);
    }

    public function test_chat_is_assigned_to_admin_and_staff_routes_are_removed(): void
    {
        $this->assertTrue(
            Schema::hasColumn('chat_conversations', 'admin_id')
        );
        $this->assertFalse(
            Schema::hasColumn('chat_conversations', 'staff_id')
        );

        $this->assertTrue(Route::has('admin.chat.index'));

        foreach (Route::getRoutes() as $route) {
            $name = (string) $route->getName();
            $uri = trim($route->uri(), '/');

            $this->assertFalse(str_starts_with($name, 'staff.'));
            $this->assertFalse(str_starts_with($uri, 'staff/'));
        }
    }

    public function test_application_uses_vietnam_timezone(): void
    {
        $this->assertSame(
            'Asia/Ho_Chi_Minh',
            config('app.timezone')
        );
        $this->assertSame(
            'Asia/Ho_Chi_Minh',
            date_default_timezone_get()
        );
    }
}
