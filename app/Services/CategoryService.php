<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    public function createCategory(string $name, ?int $parentId = null): Category
    {
        $data = ['name' => $name];

        if ($parentId !== null) {
            // Verify parent exists
            if (!$this->categoryRepository->findById($parentId)) {
                throw new \InvalidArgumentException("Parent category with ID {$parentId} does not exist.");
            }
            $data['parent_id'] = $parentId;
        }

        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, string $name, ?int $parentId = null): Category
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new \InvalidArgumentException("Category with ID {$id} does not exist.");
        }

        // Prevent circular parent references
        if ($parentId !== null) {
            if ($parentId === $id) {
                throw new \InvalidArgumentException("Category cannot be its own parent.");
            }

            $parent = $this->categoryRepository->findById($parentId);
            if (!$parent) {
                throw new \InvalidArgumentException("Parent category with ID {$parentId} does not exist.");
            }

            // Check for circular hierarchy - new parent cannot be a descendant of this category
            if ($this->isDescendant($parentId, $id)) {
                throw new \InvalidArgumentException("Setting this parent would create a circular hierarchy.");
            }
        }

        $category->update([
            'name' => $name,
            'parent_id' => $parentId,
        ]);

        return $category->fresh();
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new \InvalidArgumentException("Category with ID {$id} does not exist.");
        }

        // Cascade delete: move child categories to this category's parent
        if ($category->children->isNotEmpty()) {
            foreach ($category->children as $child) {
                $child->update(['parent_id' => $category->parent_id]);
            }
        }

        return $category->delete();
    }

    public function getWithHierarchy(): array
    {
        $roots = $this->categoryRepository->getRootCategories();

        return $roots->map(function ($root) {
            return $this->buildHierarchy($root);
        })->toArray();
    }

    private function buildHierarchy(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'children' => $category->children->map(fn ($child) => $this->buildHierarchy($child))->toArray(),
        ];
    }

    private function isDescendant(int $potentialDescendantId, int $categoryId): bool
    {
        $descendant = $this->categoryRepository->findById($potentialDescendantId);

        if (!$descendant) {
            return false;
        }

        // Walk up the hierarchy from descendant to see if we reach categoryId
        $current = $descendant;
        while ($current->parent_id !== null) {
            if ($current->parent_id === $categoryId) {
                return true;
            }
            $current = $this->categoryRepository->findById($current->parent_id);
            if (!$current) {
                break;
            }
        }

        return false;
    }
}
