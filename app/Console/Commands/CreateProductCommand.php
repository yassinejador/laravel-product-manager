<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use App\Repositories\CategoryRepository;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class CreateProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:create
        {name : The product name}
        {description : The product description}
        {price : The product price}
        {--categories= : Comma-separated list of category IDs}
        {--image= : Path to the product image file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new product via CLI';

    public function __construct(
        private ProductService $productService,
        private CategoryRepository $categoryRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $description = $this->argument('description');
        $price = (float) $this->argument('price');
        $imagePath = $this->option('image');
        $categoriesOption = $this->option('categories');

        // Validate price
        if ($price <= 0) {
            $this->error('Price must be greater than 0.');
            return self::FAILURE;
        }

        // Parse category IDs
        $categoryIds = [];
        if ($categoriesOption) {
            $categoryIds = array_filter(
                array_map('trim', explode(',', $categoriesOption))
            );

            // Validate that all categories exist
            foreach ($categoryIds as $categoryId) {
                if (!$this->categoryRepository->findById((int) $categoryId)) {
                    $this->error("Category ID {$categoryId} does not exist.");
                    return self::FAILURE;
                }
            }

            $categoryIds = array_map('intval', $categoryIds);
        }

        // Handle image file if provided
        $uploadedFile = null;
        if ($imagePath) {
            if (!file_exists($imagePath)) {
                $this->error("Image file not found: {$imagePath}");
                return self::FAILURE;
            }

            // Convert file to UploadedFile for consistency with ProductService
            $file = new SymfonyFile($imagePath);
            $uploadedFile = new UploadedFile(
                $imagePath,
                basename($imagePath),
                $file->getMimeType(),
                null,
                true
            );
        }

        try {
            $product = $this->productService->createProduct(
                $name,
                $description,
                $price,
                $categoryIds,
                $uploadedFile
            );

            $this->info("Product created successfully!");
            $this->line("ID: {$product->id}");
            $this->line("Name: {$product->name}");
            $this->line("Price: \${$product->price}");

            if ($product->categories->isNotEmpty()) {
                $categoryNames = $product->categories->pluck('name')->join(', ');
                $this->line("Categories: {$categoryNames}");
            }

            if ($product->image) {
                $this->line("Image: {$product->image}");
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create product: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
