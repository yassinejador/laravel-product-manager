<?php

namespace Tests\Unit;

use App\Console\Commands\CreateProductCommand;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Services\ProductService;
use Illuminate\Http\UploadedFile;
use Tests\DatabaseTestCase;

class CreateProductCommandTest extends DatabaseTestCase
{
    /**
     * Test creating a product with required fields only
     */
    public function test_create_product_with_required_fields_only(): void
    {
        $this->artisan('product:create', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => '29.99',
        ])
            ->assertExitCode(0)
            ->expectsOutput('Product created successfully!')
            ->expectsOutput('Name: Test Product')
            ->expectsOutput('Price: $29.99');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 29.99,
        ]);
    }

    /**
     * Test creating a product with categories
     */
    public function test_create_product_with_categories(): void
    {
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Gadgets']);

        $this->artisan('product:create', [
            'name' => 'Smart Watch',
            'description' => 'A wearable device',
            'price' => '199.99',
            '--categories' => "{$category1->id},{$category2->id}",
        ])
            ->assertExitCode(0)
            ->expectsOutput('Product created successfully!')
            ->expectsOutput('Categories: Electronics, Gadgets');

        $product = Product::where('name', 'Smart Watch')->first();
        $this->assertNotNull($product);
        $this->assertTrue($product->categories->contains($category1));
        $this->assertTrue($product->categories->contains($category2));
    }

    /**
     * Test creating a product with image
     */
    public function test_create_product_with_image(): void
    {
        $imagePath = storage_path('test_image.txt');
        file_put_contents($imagePath, 'test image content');

        try {
            $this->artisan('product:create', [
                'name' => 'Product with Image',
                'description' => 'Has an image',
                'price' => '49.99',
                '--image' => $imagePath,
            ])
                ->assertExitCode(0)
                ->expectsOutput('Product created successfully!');

            $product = Product::where('name', 'Product with Image')->first();
            $this->assertNotNull($product);
            $this->assertNotNull($product->image);
            $this->assertStringStartsWith('products/', $product->image);
        } finally {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

    /**
     * Test command fails with invalid price (zero)
     */
    public function test_create_product_fails_with_zero_price(): void
    {
        $this->artisan('product:create', [
            'name' => 'Invalid Product',
            'description' => 'Bad price',
            'price' => '0',
        ])
            ->assertExitCode(1)
            ->expectsOutput('Price must be greater than 0.');
    }

    /**
     * Test command fails with negative price
     */
    public function test_create_product_fails_with_negative_price(): void
    {
        $this->artisan('product:create', [
            'name' => 'Invalid Product',
            'description' => 'Bad price',
            'price' => '-10.00',
        ])
            ->assertExitCode(1)
            ->expectsOutput('Price must be greater than 0.');
    }

    /**
     * Test command fails with non-existent category
     */
    public function test_create_product_fails_with_nonexistent_category(): void
    {
        $this->artisan('product:create', [
            'name' => 'Product',
            'description' => 'Description',
            'price' => '29.99',
            '--categories' => '9999',
        ])
            ->assertExitCode(1)
            ->expectsOutput('Category ID 9999 does not exist.');
    }

    /**
     * Test command fails with non-existent image file
     */
    public function test_create_product_fails_with_nonexistent_image(): void
    {
        $this->artisan('product:create', [
            'name' => 'Product',
            'description' => 'Description',
            'price' => '29.99',
            '--image' => '/nonexistent/path/image.jpg',
        ])
            ->assertExitCode(1)
            ->expectsOutput('Image file not found: /nonexistent/path/image.jpg');
    }

    /**
     * Test creating a product with all options
     */
    public function test_create_product_with_all_options(): void
    {
        $category = Category::factory()->create(['name' => 'Premium']);
        $imagePath = storage_path('test_premium.txt');
        file_put_contents($imagePath, 'premium product image');

        try {
            $this->artisan('product:create', [
                'name' => 'Premium Gadget',
                'description' => 'A premium product with everything',
                'price' => '299.99',
                '--categories' => (string)$category->id,
                '--image' => $imagePath,
            ])
                ->assertExitCode(0)
                ->expectsOutput('Product created successfully!')
                ->expectsOutput('Name: Premium Gadget')
                ->expectsOutput('Price: $299.99')
                ->expectsOutput('Categories: Premium');

            $product = Product::where('name', 'Premium Gadget')->first();
            $this->assertNotNull($product);
            $this->assertTrue($product->categories->contains($category));
            $this->assertNotNull($product->image);
            $this->assertStringStartsWith('products/', $product->image);
        } finally {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

    /**
     * Test command with multiple categories
     */
    public function test_create_product_with_multiple_categories(): void
    {
        $cat1 = Category::factory()->create(['name' => 'Category 1']);
        $cat2 = Category::factory()->create(['name' => 'Category 2']);
        $cat3 = Category::factory()->create(['name' => 'Category 3']);

        $this->artisan('product:create', [
            'name' => 'Multi-Category Product',
            'description' => 'Belongs to multiple categories',
            'price' => '79.99',
            '--categories' => "{$cat1->id},{$cat2->id},{$cat3->id}",
        ])
            ->assertExitCode(0)
            ->expectsOutput('Product created successfully!')
            ->expectsOutput('Categories: Category 1, Category 2, Category 3');
    }

    /**
     * Test command handles whitespace in category IDs
     */
    public function test_create_product_handles_whitespace_in_categories(): void
    {
        $category1 = Category::factory()->create(['name' => 'Test Category 1']);
        $category2 = Category::factory()->create(['name' => 'Test Category 2']);

        $this->artisan('product:create', [
            'name' => 'Product',
            'description' => 'Description',
            'price' => '29.99',
            '--categories' => " {$category1->id} , {$category2->id} ",
        ])
            ->assertExitCode(0)
            ->expectsOutput('Product created successfully!');

        $product = Product::where('name', 'Product')->first();
        $this->assertNotNull($product);
        $this->assertEquals(2, $product->categories()->count());
        $this->assertTrue($product->categories->contains($category1));
        $this->assertTrue($product->categories->contains($category2));
    }

    /**
     * Test product is persisted with correct data
     */
    public function test_product_is_persisted_correctly(): void
    {
        $this->artisan('product:create', [
            'name' => 'Persisted Product',
            'description' => 'Should be in database',
            'price' => '49.50',
        ])
            ->assertExitCode(0);

        $product = Product::where('name', 'Persisted Product')->first();
        $this->assertNotNull($product);
        $this->assertEquals('Should be in database', $product->description);
        $this->assertEquals(49.50, $product->price);
    }
}
