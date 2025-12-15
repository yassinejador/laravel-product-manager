<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Services\CategoryService;
use Tests\DatabaseTestCase;

class CategoryServiceTest extends DatabaseTestCase
{
    private CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryService::class);
    }

    /**
     * Test creating a root category
     */
    public function test_create_root_category(): void
    {
        $category = $this->categoryService->createCategory('Electronics');

        $this->assertNotNull($category->id);
        $this->assertEquals('Electronics', $category->name);
        $this->assertNull($category->parent_id);
        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'parent_id' => null,
        ]);
    }

    /**
     * Test creating a nested category
     */
    public function test_create_nested_category(): void
    {
        $parent = Category::factory()->create(['name' => 'Electronics']);

        $child = $this->categoryService->createCategory('Smartphones', $parent->id);

        $this->assertEquals('Smartphones', $child->name);
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertDatabaseHas('categories', [
            'name' => 'Smartphones',
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * Test creating category with non-existent parent fails
     */
    public function test_create_category_with_nonexistent_parent_fails(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parent category with ID 9999 does not exist.');

        $this->categoryService->createCategory('Child', 9999);
    }

    /**
     * Test updating category name
     */
    public function test_update_category_name(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $updated = $this->categoryService->updateCategory($category->id, 'New Name');

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    /**
     * Test updating category parent
     */
    public function test_update_category_parent(): void
    {
        $oldParent = Category::factory()->create(['name' => 'Old Parent']);
        $newParent = Category::factory()->create(['name' => 'New Parent']);
        $category = Category::factory()->create([
            'name' => 'Child',
            'parent_id' => $oldParent->id,
        ]);

        $updated = $this->categoryService->updateCategory(
            $category->id,
            'Child',
            $newParent->id
        );

        $this->assertEquals($newParent->id, $updated->parent_id);
    }

    /**
     * Test updating category fails if parent is non-existent
     */
    public function test_update_category_with_nonexistent_parent_fails(): void
    {
        $category = Category::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parent category with ID 9999 does not exist.');

        $this->categoryService->updateCategory($category->id, 'Name', 9999);
    }

    /**
     * Test category cannot be its own parent
     */
    public function test_category_cannot_be_own_parent(): void
    {
        $category = Category::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category cannot be its own parent.');

        $this->categoryService->updateCategory($category->id, 'Name', $category->id);
    }

    /**
     * Test circular hierarchy is prevented
     */
    public function test_circular_hierarchy_prevented(): void
    {
        $root = Category::factory()->create(['name' => 'Root', 'parent_id' => null]);
        $parent = Category::factory()->create([
            'name' => 'Parent',
            'parent_id' => $root->id,
        ]);
        $child = Category::factory()->create([
            'name' => 'Child',
            'parent_id' => $parent->id,
        ]);

        // Try to make parent a child of child (circular)
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Setting this parent would create a circular hierarchy.');

        $this->categoryService->updateCategory($parent->id, 'Parent', $child->id);
    }

    /**
     * Test deleting category with children moves them to parent
     */
    public function test_delete_category_cascade_to_parent(): void
    {
        $root = Category::factory()->create(['name' => 'Root']);
        $parent = Category::factory()->create([
            'name' => 'Parent',
            'parent_id' => $root->id,
        ]);
        $child1 = Category::factory()->create([
            'name' => 'Child 1',
            'parent_id' => $parent->id,
        ]);
        $child2 = Category::factory()->create([
            'name' => 'Child 2',
            'parent_id' => $parent->id,
        ]);

        $this->categoryService->deleteCategory($parent->id);

        // Parent should be deleted
        $this->assertNull(Category::find($parent->id));

        // Children should now be children of root
        $this->assertEquals($root->id, $child1->fresh()->parent_id);
        $this->assertEquals($root->id, $child2->fresh()->parent_id);
    }

    /**
     * Test deleting root category with children moves them to null
     */
    public function test_delete_root_category_cascade_children_to_null(): void
    {
        $root = Category::factory()->create(['name' => 'Root', 'parent_id' => null]);
        $child = Category::factory()->create([
            'name' => 'Child',
            'parent_id' => $root->id,
        ]);

        $this->categoryService->deleteCategory($root->id);

        $this->assertNull(Category::find($root->id));
        $this->assertNull($child->fresh()->parent_id);
    }

    /**
     * Test deleting non-existent category fails
     */
    public function test_delete_nonexistent_category_fails(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category with ID 9999 does not exist.');

        $this->categoryService->deleteCategory(9999);
    }

    /**
     * Test get with hierarchy returns correct tree structure
     */
    public function test_get_with_hierarchy_returns_tree(): void
    {
        $root1 = Category::factory()->create(['name' => 'Root 1']);
        $child1 = Category::factory()->create([
            'name' => 'Child 1',
            'parent_id' => $root1->id,
        ]);
        $grandchild = Category::factory()->create([
            'name' => 'Grandchild',
            'parent_id' => $child1->id,
        ]);

        $hierarchy = $this->categoryService->getWithHierarchy();

        $this->assertIsArray($hierarchy);
        $this->assertNotEmpty($hierarchy);

        // Find root1 in hierarchy
        $root = collect($hierarchy)->firstWhere('name', 'Root 1');
        $this->assertNotNull($root);
        $this->assertEquals($root1->id, $root['id']);
        $this->assertNotEmpty($root['children']);

        // Find child1 in root's children
        $child = collect($root['children'])->firstWhere('name', 'Child 1');
        $this->assertNotNull($child);
        $this->assertEquals($child1->id, $child['id']);
        $this->assertNotEmpty($child['children']);

        // Find grandchild
        $grandchildFound = collect($child['children'])->firstWhere('name', 'Grandchild');
        $this->assertNotNull($grandchildFound);
    }

    /**
     * Test hierarchy only includes root categories at top level
     */
    public function test_hierarchy_only_includes_root_categories(): void
    {
        $root1 = Category::factory()->create(['name' => 'Root 1']);
        $root2 = Category::factory()->create(['name' => 'Root 2']);
        $child = Category::factory()->create([
            'name' => 'Child',
            'parent_id' => $root1->id,
        ]);

        $hierarchy = $this->categoryService->getWithHierarchy();

        $rootNames = array_column($hierarchy, 'name');
        $this->assertContains('Root 1', $rootNames);
        $this->assertContains('Root 2', $rootNames);
        $this->assertNotContains('Child', $rootNames);
    }

    /**
     * Test updating non-existent category fails
     */
    public function test_update_nonexistent_category_fails(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category with ID 9999 does not exist.');

        $this->categoryService->updateCategory(9999, 'Name');
    }
}
