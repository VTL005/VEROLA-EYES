<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_enable_ai_mode_for_an_open_conversation(): void
    {
        [$admin, $customer] = $this->users();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->patch(route(
                'admin.chat.mode.update',
                $conversation
            ), [
                'chat_mode' => ChatConversation::MODE_AUTO,
            ])
            ->assertRedirect(
                route('admin.chat.show', $conversation)
            );

        $this->assertDatabaseHas('chat_conversations', [
            'id' => $conversation->id,
            'admin_id' => $admin->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
        ]);
    }

    public function test_manual_mode_claims_an_unassigned_conversation(): void
    {
        [$admin, $customer] = $this->users();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->patch(route(
                'admin.chat.mode.update',
                $conversation
            ), [
                'chat_mode' => ChatConversation::MODE_MANUAL,
            ])
            ->assertRedirect();

        $conversation->refresh();

        $this->assertSame($admin->id, $conversation->admin_id);
        $this->assertTrue($conversation->isManualMode());
        $this->assertNotNull($conversation->human_takeover_at);
    }

    public function test_accepting_an_ai_conversation_keeps_ai_auto_mode(): void
    {
        [$admin, $customer] = $this->users();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.chat.accept', $conversation))
            ->assertRedirect(route('admin.chat.show', $conversation));

        $this->assertDatabaseHas('chat_conversations', [
            'id' => $conversation->id,
            'admin_id' => $admin->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
            'human_takeover_at' => null,
        ]);
    }

    public function test_another_admin_cannot_change_an_assigned_conversation_mode(): void
    {
        [$admin, $customer] = $this->users();
        $otherAdmin = $this->makeUser('admin');

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
            'status' => 'open',
        ]);

        $this->actingAs($otherAdmin)
            ->patch(route(
                'admin.chat.mode.update',
                $conversation
            ), [
                'chat_mode' => ChatConversation::MODE_AUTO,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('chat_conversations', [
            'id' => $conversation->id,
            'chat_mode' => ChatConversation::MODE_MANUAL,
        ]);
    }

    /**
     * @return array{User, User}
     */
    private function users(): array
    {
        $this->seed(RoleSeeder::class);

        return [
            $this->makeUser('admin'),
            $this->makeUser('customer'),
        ];
    }

    private function makeUser(string $roleName): User
    {
        return User::factory()->create([
            'role_id' => Role::query()
                ->where('name', $roleName)
                ->valueOrFail('id'),
            'phone' => fake()->unique()->numerify('09########'),
            'is_active' => true,
        ]);
    }
}
