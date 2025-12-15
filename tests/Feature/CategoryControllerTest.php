<?php

namespace Tests\Feature;

use App\Models\Category;
use Tests\DatabaseTestCase;

class CategoryControllerTest extends DatabaseTestCase
{
    /**
     * Test viewing categories index
     */
    public function test_view_categories_index(): void
    {
        Category::factory(3)->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('categories.index');
        $response->assertViewHas('categories');
        $response->assertViewHas('hierarchyData');
    }

    /**
     * Test viewing empty categories index
     */
    public function test_view_empty_categories_index(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSeeText('No categories yet');
    }

    /**
     * Test viewing create category form
     */
    public function test_view_create_category_form(): void
    {
        $response = $this->get(route('categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('categories.create');
        $response->assertSee('Create Category');
    }

    /**
     * Test creating a category
     */
    public function test_create_category(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'Electronics',
        ]);

        $response->assertRedirectToRoute('categories.index');
        $response->assertSessionHas('success', 'Category created successfully.');
        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'parent_id' => null,
        ]);
    }

    /**
     * Test creating nested category
     */
    public function test_create_nested_category(): void
    {
        $parent = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->post(route('categories.store'), [
            'name' => 'Smartphones',
            'parent_id' => $parent->id,
        ]);

        $response->assertRedirectToRoute('categories.index');
        $this->assertDatabaseHas('categories', [
            'name' => 'Smartphones',
            'parent_id' => $parent->id,
        ]);
    }

    /**
     * Test creating category with empty name fails
     */
    public function test_create_category_with_empty_name_fails(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $response->assertRedirect();
    }

    /**
     * Test creating category with duplicate name fails
     */
    public function test_create_category_with_duplicate_name_fails(): void
    {
        Category::factory()->create(['name' => 'Electronics']);

        $response = $this->post(route('categories.store'), [
            'name' => 'Electronics',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test creating category with non-existent parent fails
     */
    public function test_create_category_with_nonexistent_parent_fails(): void
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'Child',
            'parent_id' => 9999,
        ]);

        $response->assertSessionHasErrors('parent_id');
    }

    /**
     * Test viewing category details
     */
    public function test_view_category_details(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->get(route('categories.show', $category));

        $response->assertStatus(200);
        $response->assertViewIs('categories.show');
        $response->assertSee('Electronics');
    }

    /**
     * Test viewing category with products
     */
    public function test_view_category_with_products(): void
    {
        $category = Category::factory()->create();
        $products = $category->products()->createMany([
            ['name' => 'Product 1', 'description' => 'Desc', 'price' => 10.00],
            ['name' => 'Product 2', 'description' => 'Desc', 'price' => 20.00],
        ]);

        $response = $this->get(route('categories.show', $category));

        $response->assertStatus(200);
        $response->assertSee('Product 1');
        $response->assertSee('Product 2');
    }

    /**
     * Test viewing category with subcategories
     */
    public function test_view_category_with_subcategories(): void
    {
        $parent = Category::factory()->create(['name' => 'Electronics']);
        $child1 = Category::factory()->create([
            'name' => 'Phones',
            'parent_id' => $parent->id,
        ]);
        $child2 = Category::factory()->create([
            'name' => 'Tablets',
            'parent_id' => $parent->id,
        ]);

        $response = $this->get(route('categories.show', $parent));

        $response->assertStatus(200);
        $response->assertSee('Phones');
        $response->assertSee('Tablets');
        $response->assertSee('Subcategories');
    }

    /**
     * Test viewing edit category form
     */
    public function test_view_edit_category_form(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->get(route('categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewIs('categories.edit');
        $response->assertSee('Electronics');
        $response->assertSee('Edit Category');
    }

    /**
     * Test updating category
     */
    public function test_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'New Name',
        ]);

        $response->assertRedirectToRoute('categories.show', $category);
        $response->assertSessionHas('success', 'Category updated successfully.');
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
            'name' => 'Test Child Category',
            'parent_id' => $oldParent->id,
        ]);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Test Child Category',
            'parent_id' => $newParent->id,
        ]);

        $response->assertRedirectToRoute('categories.show', $category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => $newParent->id,
        ]);
    }

    /**
     * Test updating category to root
     */
    public function test_update_category_to_root(): void
    {
        $parent = Category::factory()->create();
        $category = Category::factory()->create([
            'name' => 'Test Root Category',
            'parent_id' => $parent->id,
        ]);

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Test Root Category',
            'parent_id' => null,
        ]);

        $response->assertRedirectToRoute('categories.show', $category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'parent_id' => null,
        ]);
    }

    /**
     * Test deleting category
     */
    public function test_delete_category(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertRedirectToRoute('categories.index');
        $response->assertSessionHas('success', 'Category deleted successfully.');
        $this->assertModelMissing($category);
    }

    /**
     * Test deleting category with children cascades
     */
    public function test_delete_category_with_children_cascades(): void
    {
        $parent = Category::factory()->create(['name' => 'Parent']);
        $child1 = Category::factory()->create([
            'name' => 'Child 1',
            'parent_id' => $parent->id,
        ]);
        $child2 = Category::factory()->create([
            'name' => 'Child 2',
            'parent_id' => $parent->id,
        ]);

        $response = $this->delete(route('categories.destroy', $parent));

        $response->assertRedirectToRoute('categories.index');
        $this->assertModelMissing($parent);
        // Children should be preserved
        $this->assertModelExists($child1);
        $this->assertModelExists($child2);
        // But their parent should be null
        $this->assertNull($child1->fresh()->parent_id);
        $this->assertNull($child2->fresh()->parent_id);
    }
}
