<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private CategoryRepository $categoryRepository
    ) {
    }

    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = $this->categoryRepository->all();
        $hierarchyData = $this->categoryService->getWithHierarchy();

        return view('categories.index', compact('categories', 'hierarchyData'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $categories = $this->categoryRepository->all();

        return view('categories.create', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(CreateCategoryRequest $request): RedirectResponse
    {
        try {
            $this->categoryService->createCategory(
                $request->input('name'),
                $request->input('parent_id')
            );

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $category->load('children', 'parent', 'products');

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $categories = $this->categoryRepository->all()
            ->where('id', '!=', $category->id)
            ->values();

        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category.
     */
    public function update(CreateCategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            $this->categoryService->updateCategory(
                $category->id,
                $request->input('name'),
                $request->input('parent_id')
            );

            return redirect()->route('categories.show', $category)
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            $this->categoryService->deleteCategory($category->id);

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
