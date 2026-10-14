<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Review;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_and_update_an_official_review_reply(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        $category = Category::create(['name' => 'Gọng kính', 'slug' => 'gong-kinh', 'is_active' => true]);
        $product = $category->products()->create([
            'name' => 'Mẫu kiểm thử', 'slug' => 'mau-kiem-thu', 'sku' => 'TEST-REPLY-01',
            'price' => 500000, 'shape' => 'oval', 'gender' => 'unisex', 'is_active' => true,
        ]);
        $review = Review::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'rating' => 3,
            'comment' => 'Sản phẩm đẹp.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reviews.reply', $review), ['content' => 'VELORA cảm ơn bạn đã chia sẻ trải nghiệm.'])
            ->assertRedirect(route('admin.reviews.show', $review));

        $this->assertDatabaseHas('review_replies', [
            'review_id' => $review->id,
            'admin_id' => $admin->id,
            'content' => 'VELORA cảm ơn bạn đã chia sẻ trải nghiệm.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reviews.reply', $review), ['content' => 'VELORA đã cập nhật hỗ trợ cho bạn.'])
            ->assertRedirect(route('admin.reviews.show', $review));

        $this->assertDatabaseCount('review_replies', 1);
        $this->assertDatabaseHas('review_replies', ['review_id' => $review->id, 'content' => 'VELORA đã cập nhật hỗ trợ cho bạn.']);
    }

    private function makeUser(string $role): User
    {
        return User::factory()->create([
            'role_id' => Role::query()->where('name', $role)->valueOrFail('id'),
            'phone' => $role === 'admin' ? '0900000000' : '0900000001',
            'is_active' => true,
        ]);
    }
}
