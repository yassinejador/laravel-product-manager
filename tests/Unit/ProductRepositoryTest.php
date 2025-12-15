<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Tests\DatabaseTestCase;

class ProductRepositoryTest extends DatabaseTestCase
{
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(ProductRepository::class);
    }

    public function test_create_product(): void
    {
        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ];

        $product = $this->repository->create($data);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($data['name'], $product->name);
        $this->assertDatabaseHas('products', $data);
    }

    public function test_find_product_by_id(): void
    {
        $product = Product::factory()->create();

        $found = $this->repository->findById($product->id);

        $this->assertNotNull($found);
        $this->assertEquals($product->id, $found->id);
        $this->assertEquals($product->name, $found->name);
    }

    public function test_find_product_by_id_returns_null_for_nonexistent(): void
    {
        $found = $this->repository->findById(99999);

        $this->assertNull($found);
    }

    public function test_get_all_products(): void
    {
        Product::factory()->count(3)->create();

        $all = $this->repository->all();

        $this->assertCount(3, $all);
    }

    public function test_attach_categories_to_product(): void
    {
        $product = Product::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $this->repository->attachCategories($product, $categoryIds);

        $this->assertCount(2, $product->categories);
        $this->assertTrue($product->categories->contains($categories[0]));
        $this->assertTrue($product->categories->contains($categories[1]));
    }

    public function test_get_filtered_by_category(): void
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $product1->categories()->attach($category->id);

        $query = $this->repository->getFiltered(['category_id' => $category->id]);
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($product1));
        $this->assertFalse($results->contains($product2));
    }

    public function test_get_filtered_sort_by_price_ascending(): void
    {
        Product::factory()->create(['price' => 100.00]);
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 75.00]);

        $query = $this->repository->getFiltered([
            'sort_by' => 'price',
            'sort_direction' => 'asc',
        ]);
        $results = $query->get();

        $this->assertEquals(50.00, $results[0]->price);
        $this->assertEquals(75.00, $results[1]->price);
        $this->assertEquals(100.00, $results[2]->price);
    }

    public function test_get_filtered_sort_by_price_descending(): void
    {
        Product::factory()->create(['price' => 100.00]);
        Product::factory()->create(['price' => 50.00]);
        Product::factory()->create(['price' => 75.00]);

        $query = $this->repository->getFiltered([
            'sort_by' => 'price',
            'sort_direction' => 'desc',
        ]);
        $results = $query->get();

        $this->assertEquals(100.00, $results[0]->price);
        $this->assertEquals(75.00, $results[1]->price);
        $this->assertEquals(50.00, $results[2]->price);
    }

    public function test_get_filtered_combined_category_and_sort(): void
    {
        $category = Category::factory()->create();
        $product1 = Product::factory()->create(['price' => 100.00]);
        $product2 = Product::factory()->create(['price' => 50.00]);
        $product3 = Product::factory()->create(['price' => 75.00]);

        $product1->categories()->attach($category->id);
        $product2->categories()->attach($category->id);

        $query = $this->repository->getFiltered([
            'category_id' => $category->id,
            'sort_by' => 'price',
            'sort_direction' => 'asc',
        ]);
        $results = $query->get();

        $this->assertCount(2, $results);
        $this->assertEquals(50.00, $results[0]->price);
        $this->assertEquals(100.00, $results[1]->price);
    }
}
