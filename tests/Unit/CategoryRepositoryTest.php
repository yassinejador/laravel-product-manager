<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Tests\DatabaseTestCase;

class CategoryRepositoryTest extends DatabaseTestCase
{
    private CategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(CategoryRepository::class);
    }

    public function test_create_category(): void
    {
        $data = [
            'name' => 'Electronics',
        ];

        $category = $this->repository->create($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($data['name'], $category->name);
        $this->assertDatabaseHas('categories', $data);
    }

    public function test_find_category_by_id(): void
    {
        $category = Category::factory()->create();

        $found = $this->repository->findById($category->id);

        $this->assertNotNull($found);
        $this->assertEquals($category->id, $found->id);
        $this->assertEquals($category->name, $found->name);
    }

    public function test_find_category_by_id_returns_null_for_nonexistent(): void
    {
        $found = $this->repository->findById(99999);

        $this->assertNull($found);
    }

    public function test_get_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $all = $this->repository->all();

        $this->assertCount(3, $all);
    }

    public function test_get_root_categories(): void
    {
        $root1 = Category::factory()->create(['parent_id' => null]);
        $root2 = Category::factory()->create(['parent_id' => null]);
        $child = Category::factory()->create(['parent_id' => $root1->id]);

        $roots = $this->repository->getRootCategories();

        $this->assertCount(2, $roots);
        $this->assertTrue($roots->contains($root1));
        $this->assertTrue($roots->contains($root2));
        $this->assertFalse($roots->contains($child));
    }

    public function test_category_hierarchy(): void
    {
        $parent = Category::factory()->create(['name' => 'Electronics']);
        $child = Category::factory()->create([
            'name' => 'Laptops',
            'parent_id' => $parent->id,
        ]);

        $foundParent = $this->repository->findById($parent->id);
        $foundChild = $this->repository->findById($child->id);

        $this->assertEquals($parent->id, $foundChild->parent_id);
        $this->assertTrue($foundParent->children->contains($foundChild));
    }
}
