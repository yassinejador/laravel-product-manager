<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    public function index(): View
    {
        $filters = [
            'category_id' => request()->query('category_id'),
            'sort_by' => request()->query('sort_by'),
            'sort_direction' => request()->query('sort_direction', 'asc'),
        ];

        $query = $this->productRepository->getFiltered($filters);
        $products = $query->paginate(15);
        $categories = $this->categoryRepository->all();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $filters['category_id'],
            'sortBy' => $filters['sort_by'],
            'sortDirection' => $filters['sort_direction'],
        ]);
    }

    public function create(): View
    {
        $categories = $this->categoryRepository->all();

        return view('products.create', [
            'categories' => $categories,
        ]);
    }

    public function store(CreateProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct(
            name: $request->validated('name'),
            description: $request->validated('description'),
            price: (float) $request->validated('price'),
            categoryIds: $request->validated('categories', []),
            image: $request->file('image')
        );

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(string $id): View
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            abort(404);
        }

        return view('products.show', ['product' => $product]);
    }

    public function edit(string $id): View
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            abort(404);
        }

        $categories = $this->categoryRepository->all();

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(CreateProductRequest $request, string $id): RedirectResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            abort(404);
        }

        $product->update($request->validated(['name', 'description', 'price']));

        if ($request->validated('categories')) {
            $this->productRepository->attachCategories($product, $request->validated('categories'));
        }

        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            abort(404);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
