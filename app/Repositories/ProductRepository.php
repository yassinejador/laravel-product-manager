<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function __construct(private Product $model)
    {
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function attachCategories(Product $product, array $categoryIds): void
    {
        $product->categories()->sync($categoryIds);
    }

    public function getFiltered(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->model->query();

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (!empty($filters['sort_by'])) {
            $sortBy = $filters['sort_by'];
            $direction = $filters['sort_direction'] ?? 'asc';

            if ($sortBy === 'price') {
                $query->orderBy('price', $direction);
            }
        }

        return $query;
    }
}
