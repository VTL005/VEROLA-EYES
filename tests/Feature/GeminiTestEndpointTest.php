<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiTestEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_test_the_gemini_connection(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'role_id' => Role::query()
                ->where('name', 'admin')
                ->valueOrFail('id'),
            'phone' => '0900000000',
            'is_active' => true,
        ]);

        config()->set('services.apiz.api_key', 'test-key');
        config()->set('services.apiz.model', 'gemini-test');
        config()->set(
            'services.apiz.base_url',
            'https://api.apiz.vn/v1'
        );

        Http::fake([
            'https://api.apiz.vn/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => 'Xin chào từ VELORA AI.',
                    ],
                ]],
            ]),
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.gemini.test'))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Xin chào từ VELORA AI.',
            ]);

        Http::assertSent(function ($request): bool {
            return $request->hasHeader('Authorization', 'Bearer test-key')
                && data_get(
                    $request->data(),
                    'messages.0.content'
                ) !== null
                && data_get(
                    $request->data(),
                    'max_tokens'
                ) === 512
                && data_get(
                    $request->data(),
                    'temperature'
                ) === 0.45;
        });
    }

    public function test_guests_cannot_call_the_gemini_test_endpoint(): void
    {
        $this->getJson(route('admin.gemini.test'))
            ->assertUnauthorized();
    }
}
