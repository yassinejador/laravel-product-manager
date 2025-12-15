@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-bag-fill"></i> Products</h1>
                <p class="mt-2">Manage your product catalog efficiently</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel"></i> Filter & Sort
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategory == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="sort_by" class="form-label">Sort By</label>
                    <select name="sort_by" id="sort_by" class="form-select">
                        <option value="">Default</option>
                        <option value="price" @selected($sortBy === 'price')>Price</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="sort_direction" class="form-label">Direction</label>
                    <select name="sort_direction" id="sort_direction" class="form-select">
                        <option value="asc" @selected($sortDirection === 'asc')>Ascending</option>
                        <option value="desc" @selected($sortDirection === 'desc')>Descending</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    @if ($products->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Categories</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($product->description, 50) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">${{ number_format($product->price, 2) }}</span>
                            </td>
                            <td>
                                @if ($product->categories->count() > 0)
                                    @foreach ($product->categories as $category)
                                        <span class="badge bg-info">{{ $category->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted small">No categories</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions justify-content-end">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this product?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h3>No products found</h3>
                    <p>Start by creating your first product or adjusting your filters</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Create Product
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
