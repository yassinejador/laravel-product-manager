<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function __construct(private Category $model)
    {
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function getRootCategories(): Collection
    {
        return $this->model->whereNull('parent_id')->get();
    }
}
