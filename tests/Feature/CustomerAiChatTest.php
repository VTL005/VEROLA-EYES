<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatAiSuggestion;
use App\Models\ChatMessage;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Services\ChatAiResponder;
use Tests\TestCase;

class CustomerAiChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_automatically_replies_to_a_customer_message(): void
    {
        $customer = $this->customer();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
            'status' => 'open',
        ]);

        $this->configureGemini();

        Http::fake([
            'https://api.apiz.vn/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => 'Chào bạn, mình có thể tư vấn kính phù hợp.',
                    ],
                ]],
            ]),
        ]);

        $this->actingAs($customer)
            ->post(route('customer.chat.store'), [
                'message' => 'Mình cần kính để làm việc với máy tính.',
            ])
            ->assertRedirect(route('customer.chat.index'));

        $this->assertDatabaseHas('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'sender_type' => ChatMessage::SENDER_CUSTOMER,
            'message' => 'Mình cần kính để làm việc với máy tính.',
        ]);

        $this->assertDatabaseHas('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => ChatMessage::SENDER_AI,
            'message' => 'Chào bạn, mình có thể tư vấn kính phù hợp.',
        ]);

        $conversation->refresh();

        $this->assertNotNull($conversation->ai_last_reply_at);

        Http::assertSent(function ($request): bool {
            return $request->hasHeader('Authorization', 'Bearer test-key')
                && str_contains(
                    (string) data_get(
                        $request->data(),
                        'messages.1.content'
                    ),
                    'Mình cần kính để làm việc với máy tính.'
                );
        });
    }

    public function test_manual_mode_does_not_send_customer_messages_to_gemini(): void
    {
        $customer = $this->customer();

        ChatConversation::create([
            'customer_id' => $customer->id,
            'chat_mode' => ChatConversation::MODE_MANUAL,
            'status' => 'open',
        ]);

        Http::preventStrayRequests();

        $this->actingAs($customer)
            ->post(route('customer.chat.store'), [
                'message' => 'Tôi cần gặp nhân viên.',
            ])
            ->assertRedirect(route('customer.chat.index'));

        Http::assertNothingSent();

        $this->assertDatabaseCount('chat_messages', 1);
    }

    public function test_a_new_customer_conversation_starts_in_ai_auto_mode(): void
    {
        $customer = $this->customer();

        $this->configureGemini();

        Http::fake();

        $this->actingAs($customer)
            ->post(route('customer.chat.store'), [
                'message' => 'Chào shop.',
            ])
            ->assertRedirect(route('customer.chat.index'));

        $this->assertDatabaseHas('chat_conversations', [
            'customer_id' => $customer->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
            'status' => 'open',
        ]);
    }

    public function test_assist_mode_creates_a_private_suggestion_instead_of_sending_ai_message(): void
    {
        $customer = $this->customer();
        $admin = $this->admin();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
            'chat_mode' => ChatConversation::MODE_ASSIST,
            'status' => 'open',
        ]);

        $customerMessage = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'sender_type' => ChatMessage::SENDER_CUSTOMER,
            'message_type' => ChatMessage::TYPE_TEXT,
            'message' => 'Mình cần kính đi chơi tối.',
        ]);

        $this->configureGemini();

        Http::fake([
            'https://api.apiz.vn/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => 'Tối đi chơi thì gọng mảnh màu đen hoặc nâu trà sẽ rất dễ phối đồ đó bạn. Bạn thích kiểu cá tính hay thanh lịch hơn?',
                    ],
                ]],
            ]),
        ]);

        app(ChatAiResponder::class)->respondTo($customerMessage);

        $this->assertDatabaseHas('chat_ai_suggestions', [
            'chat_conversation_id' => $conversation->id,
            'source_chat_message_id' => $customerMessage->id,
            'status' => ChatAiSuggestion::STATUS_PENDING,
        ]);

        $this->assertDatabaseMissing('chat_messages', [
            'chat_conversation_id' => $conversation->id,
            'sender_type' => ChatMessage::SENDER_AI,
        ]);
    }

    public function test_ai_sends_matching_product_cards_when_customer_asks_to_see_products(): void
    {
        $customer = $this->customer();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
            'status' => 'open',
        ]);

        $category = Category::create([
            'name' => 'Kính thời trang',
            'slug' => 'kinh-thoi-trang',
            'is_active' => true,
        ]);

        $matchingProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Gọng Kim Loại Đa Giác Retro',
            'slug' => 'gong-kim-loai-da-giac-retro',
            'sku' => 'AI-METAL-001',
            'price' => 890000,
            'material' => 'Kim loại',
            'shape' => 'Đa giác',
            'style_tags' => ['Cá tính', 'Retro'],
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Gọng Nhựa Tròn Tối Giản',
            'slug' => 'gong-nhua-tron-toi-gian',
            'sku' => 'AI-PLASTIC-001',
            'price' => 490000,
            'material' => 'Nhựa',
            'shape' => 'Tròn',
            'style_tags' => ['Tối giản'],
            'is_active' => true,
        ]);

        $this->configureGemini();

        Http::fake([
            'https://api.apiz.vn/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => 'Mình gửi bạn vài mẫu gọng kim loại cá tính phù hợp nhé.',
                    ],
                ]],
            ]),
        ]);

        $this->actingAs($customer)
            ->post(route('customer.chat.store'), [
                'message' => 'Bạn cho mình xem sản phẩm gọng kim loại cá tính nhé.',
            ])
            ->assertRedirect(route('customer.chat.index'));

        $aiMessage = ChatMessage::query()
            ->where('chat_conversation_id', $conversation->id)
            ->where('sender_type', ChatMessage::SENDER_AI)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(
            ChatMessage::TYPE_PRODUCT_LIST,
            $aiMessage->message_type
        );

        $this->assertTrue(
            $aiMessage->products()
                ->whereKey($matchingProduct->id)
                ->exists()
        );
    }

    public function test_ai_sends_product_cards_when_customer_confirms_an_offer_to_send_samples(): void
    {
        $customer = $this->customer();

        $conversation = ChatConversation::create([
            'customer_id' => $customer->id,
            'chat_mode' => ChatConversation::MODE_AUTO,
            'status' => 'open',
        ]);

        $category = Category::create([
            'name' => 'Gọng kính',
            'slug' => 'gong-kinh',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Gọng Kim Loại Cá Tính',
            'slug' => 'gong-kim-loai-ca-tinh',
            'sku' => 'AI-CONFIRM-001',
            'price' => 990000,
            'material' => 'Kim loại',
            'style_tags' => ['Cá tính'],
            'is_active' => true,
        ]);

        ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => ChatMessage::SENDER_AI,
            'message_type' => ChatMessage::TYPE_TEXT,
            'message' => 'Mình gửi ngay vài mẫu gọng kim loại cá tính để bạn tham khảo nhé.',
        ]);

        $customerMessage = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'sender_type' => ChatMessage::SENDER_CUSTOMER,
            'message_type' => ChatMessage::TYPE_TEXT,
            'message' => 'Oki bạn gửi đi.',
        ]);

        $this->configureGemini();

        Http::fake([
            'https://api.apiz.vn/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => 'Mình gửi bạn vài mẫu phù hợp nhé.',
                    ],
                ]],
            ]),
        ]);

        app(ChatAiResponder::class)->respondTo($customerMessage);

        $aiMessage = ChatMessage::query()
            ->where('chat_conversation_id', $conversation->id)
            ->where('sender_type', ChatMessage::SENDER_AI)
            ->latest('id')
            ->firstOrFail();

        $this->assertSame(
            ChatMessage::TYPE_PRODUCT_LIST,
            $aiMessage->message_type
        );

        $this->assertTrue(
            $aiMessage->products()
                ->whereKey($product->id)
                ->exists()
        );
    }

    private function customer(): User
    {
        $this->seed(RoleSeeder::class);

        return User::factory()->create([
            'role_id' => Role::query()
                ->where('name', 'customer')
                ->valueOrFail('id'),
            'phone' => '0900000000',
            'is_active' => true,
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role_id' => Role::query()
                ->where('name', 'admin')
                ->valueOrFail('id'),
            'phone' => fake()->unique()->numerify('09########'),
            'is_active' => true,
        ]);
    }

    private function configureGemini(): void
    {
        config()->set('services.apiz.api_key', 'test-key');
        config()->set('services.apiz.model', 'gemini-test');
        config()->set(
            'services.apiz.base_url',
            'https://api.apiz.vn/v1'
        );
    }
}
