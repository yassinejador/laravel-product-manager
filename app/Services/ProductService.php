<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function createProduct(
        string $name,
        string $description,
        float $price,
        array $categoryIds = [],
        ?UploadedFile $image = null
    ): Product {
        $data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
        ];

        if ($image !== null) {
            $data['image'] = $this->storeImage($image);
        }

        $product = $this->productRepository->create($data);

        if (!empty($categoryIds)) {
            $this->productRepository->attachCategories($product, $categoryIds);
        }

        return $product->load('categories');
    }

    private function storeImage(UploadedFile $file): string
    {
        return $file->store('products', 'public');
    }
}
