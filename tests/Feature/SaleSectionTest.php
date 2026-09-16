<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class SaleSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        view()->share('errors', new ViewErrorBag);
    }

    private function createDummyCategory(): Category
    {
        $category = new Category([
            'name' => 'Kính Mát Thời Trang',
            'slug' => 'kinh-mat-thoi-trang',
            'is_active' => true,
        ]);
        $category->id = 1;

        return $category;
    }

    private function createDummyProduct(
        int $id,
        string $name,
        int $price,
        ?int $salePrice,
        ?string $imagePath = 'images/products/sample.png'
    ): Product {
        $product = new Product([
            'name' => $name,
            'slug' => 'product-'.$id,
            'price' => $price,
            'sale_price' => $salePrice,
            'is_active' => true,
        ]);
        $product->id = $id;

        $category = $this->createDummyCategory();
        $product->setRelation('category', $category);

        if ($imagePath !== null) {
            $image = new ProductImage([
                'product_id' => $id,
                'image_path' => $imagePath,
                'is_primary' => true,
            ]);
            $product->setRelation('primaryImage', $image);
        } else {
            $product->setRelation('primaryImage', null);
        }

        return $product;
    }

    /**
     * Case 1: Không có sản phẩm ưu đãi -> Section không hiển thị
     */
    public function test_sale_section_is_hidden_when_no_sale_products(): void
    {
        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect(),
        ]);

        $view->assertDontSee('home-sale-products__grid');
        $view->assertDontSee('data-home-sale-grid', false);
    }

    /**
     * Case 2: Một sản phẩm -> class has-1 và căn giữa
     */
    public function test_sale_section_with_one_product_uses_has_1_class(): void
    {
        $p1 = $this->createDummyProduct(1, 'Kính Velora Classic One', 1000000, 800000);

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p1]),
        ]);

        $view->assertSee('home-sale-products__grid has-1', false);
        $view->assertSee('data-home-sale-grid', false);
        $view->assertSee('Kính Velora Classic One');
        $view->assertSee('-20%');
    }

    /**
     * Case 3: Hai sản phẩm -> class has-2
     */
    public function test_sale_section_with_two_products_uses_has_2_class(): void
    {
        $p1 = $this->createDummyProduct(1, 'Kính Velora Classic 1', 1000000, 800000);
        $p2 = $this->createDummyProduct(2, 'Kính Velora Classic 2', 1200000, 900000);

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p1, $p2]),
        ]);

        $view->assertSee('home-sale-products__grid has-2', false);
        $view->assertSee('Kính Velora Classic 1');
        $view->assertSee('Kính Velora Classic 2');
    }

    /**
     * Case 4: Ba sản phẩm -> class has-3 (3 cột bằng nhau)
     */
    public function test_sale_section_with_three_products_uses_has_3_class(): void
    {
        $p1 = $this->createDummyProduct(1, 'Kính Velora 1', 1000000, 800000);
        $p2 = $this->createDummyProduct(2, 'Kính Velora 2', 1200000, 900000);
        $p3 = $this->createDummyProduct(3, 'Kính Velora 3', 1500000, 1000000);

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p1, $p2, $p3]),
        ]);

        $view->assertSee('home-sale-products__grid has-3', false);
        $view->assertSee('Kính Velora 1');
        $view->assertSee('Kính Velora 2');
        $view->assertSee('Kính Velora 3');
    }

    /**
     * Case 5: Bốn sản phẩm -> class has-4
     */
    public function test_sale_section_with_four_products_uses_has_4_class(): void
    {
        $products = collect();
        for ($i = 1; $i <= 4; $i++) {
            $products->push($this->createDummyProduct($i, "Kính Velora $i", 1000000, 800000));
        }

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => $products,
        ]);

        $view->assertSee('home-sale-products__grid has-4', false);
    }

    /**
     * Case 6: Nhiều hơn 4 sản phẩm (ví dụ 6 sản phẩm) -> class has-4 tối đa 4 cột
     */
    public function test_sale_section_with_more_than_four_products_caps_at_has_4_class(): void
    {
        $products = collect();
        for ($i = 1; $i <= 6; $i++) {
            $products->push($this->createDummyProduct($i, "Kính Velora $i", 1000000, 800000));
        }

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => $products,
        ]);

        $view->assertSee('home-sale-products__grid has-4', false);
    }

    /**
     * Case 7: Sản phẩm có giảm giá -76%
     */
    public function test_sale_section_renders_76_percent_discount_badge_safely(): void
    {
        // 1.000.000 -> 240.000 = (1.000.000 - 240.000) / 1.000.000 = 76%
        $p = $this->createDummyProduct(1, 'Gọng Titan Luxury', 1000000, 240000);

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p]),
        ]);

        $view->assertSee('home-sale-product-card__discount');
        $view->assertSee('-76%');
    }

    /**
     * Case 8: Sản phẩm có tên dài không phá vỡ cấu trúc card
     */
    public function test_sale_section_handles_long_product_names(): void
    {
        $longName = 'Gọng Kính Cận Cắt Ánh Sáng Xanh Phân Cực Chống Tia UV Titan Siêu Nhẹ Phiên Bản Giới Hạn Cao Cấp 2026';
        $p = $this->createDummyProduct(1, $longName, 2500000, 1800000);

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p]),
        ]);

        $view->assertSee($longName);
        $view->assertSee('home-sale-product-card__title');
    }

    /**
     * Case 9: Sản phẩm có ảnh đường dẫn khác tỷ lệ
     */
    public function test_sale_section_renders_different_image_paths(): void
    {
        $p = $this->createDummyProduct(1, 'Kính Vuông Siêu Mỏng', 1500000, 1200000, 'images/products/custom-aspect-ratio.png');

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p]),
        ]);

        $view->assertSee('custom-aspect-ratio.png');
        $view->assertSee('home-sale-product-card__media');
    }

    /**
     * Case 10: Sản phẩm không có ảnh -> fallback về no-image.png
     */
    public function test_sale_section_handles_fallback_image_when_product_has_no_image(): void
    {
        $p = $this->createDummyProduct(1, 'Kính Không Ảnh', 1000000, 800000, null);

        $view = $this->view('home', [
            'primaryCategory' => $this->createDummyCategory(),
            'secondaryCategories' => collect(),
            'primaryProduct' => null,
            'secondaryProducts' => collect(),
            'saleProducts' => collect([$p]),
        ]);

        $view->assertSee('images/no-image.png');
    }
}
