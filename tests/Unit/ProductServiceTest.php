<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseTestCase;

class ProductServiceTest extends DatabaseTestCase
{
    private ProductService $service;
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->repository = $this->app->make(ProductRepository::class);
        $this->service = new ProductService($this->repository);
    }

    public function test_create_product_with_required_fields(): void
    {
        $product = $this->service->createProduct(
            name: 'Test Product',
            description: 'A test product',
            price: 99.99
        );

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->name);
        $this->assertEquals('A test product', $product->description);
        $this->assertEquals(99.99, $product->price);
        $this->assertNull($product->image);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
        ]);
    }

    public function test_create_product_with_image(): void
    {
        // Use a plain file if GD is not available
        if (extension_loaded('gd')) {
            $file = UploadedFile::fake()->image('product.jpg');
        } else {
            $file = UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg');
        }

        $product = $this->service->createProduct(
            name: 'Product with Image',
            description: 'Description',
            price: 49.99,
            image: $file
        );

        $this->assertNotNull($product->image);
        $this->assertTrue(Storage::disk('public')->exists($product->image));
    }

    public function test_create_product_with_categories(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $product = $this->service->createProduct(
            name: 'Categorized Product',
            description: 'With categories',
            price: 75.00,
            categoryIds: [$category1->id, $category2->id]
        );

        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($category1));
        $this->assertTrue($product->categories->contains($category2));
    }

    public function test_create_product_persists_to_database(): void
    {
        $product = $this->service->createProduct(
            name: 'Database Test',
            description: 'Testing persistence',
            price: 150.00
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Database Test',
            'price' => 150.00,
        ]);
    }

    public function test_create_product_loads_relationships(): void
    {
        $category = Category::factory()->create();

        $product = $this->service->createProduct(
            name: 'Related Product',
            description: 'Check relationships',
            price: 60.00,
            categoryIds: [$category->id]
        );

        $this->assertNotEmpty($product->categories);
    }
}
