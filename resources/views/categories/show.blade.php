@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ $category->name }}</h1>
            @if ($category->parent)
                <p class="text-muted">Parent: <a href="{{ route('categories.show', $category->parent) }}">{{ $category->parent->name }}</a></p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">Edit</a>
            <form method="POST" action="{{ route('categories.destroy', $category) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($category->children->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Subcategories</h5>
            </div>
            <div class="list-group list-group-flush">
                @foreach ($category->children as $child)
                    <a href="{{ route('categories.show', $child) }}" class="list-group-item list-group-item-action">
                        {{ $child->name }}
                        <span class="badge bg-info float-end">{{ $child->products->count() }} products</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if ($category->products->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Products in this Category</h5>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category->products as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            No products in this category yet.
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to Categories</a>
    </div>
</div>
@endsection
