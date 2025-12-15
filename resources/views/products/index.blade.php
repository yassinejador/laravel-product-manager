@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>Products</h2>
        <a href="{{ route('products.create') }}" class="btn">Create Product</a>
    </div>

    <div style="background-color: white; padding: 1.5rem; border-radius: 4px; margin-bottom: 2rem;">
        <form method="GET" action="{{ route('products.index') }}" style="display: flex; gap: 1rem; padding: 0;">
            <div style="flex: 1;">
                <label for="category_id">Filter by Category:</label>
                <select name="category_id" id="category_id" style="margin-top: 0.5rem;">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1;">
                <label for="sort_by">Sort by Price:</label>
                <select name="sort_by" id="sort_by" style="margin-top: 0.5rem;">
                    <option value="">None</option>
                    <option value="price" {{ $sortBy === 'price' ? 'selected' : '' }}>Price</option>
                </select>
            </div>

            <div style="flex: 1;">
                <label for="sort_direction">Direction:</label>
                <select name="sort_direction" id="sort_direction" style="margin-top: 0.5rem;">
                    <option value="asc" {{ $sortDirection === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ $sortDirection === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            </div>

            <div style="display: flex; align-items: flex-end;">
                <button type="submit" style="margin: 0;">Filter</button>
            </div>
        </form>
    </div>

    @if ($products->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Categories</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ Str::limit($product->description, 50) }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>
                            @if ($product->categories->count() > 0)
                                {{ $product->categories->pluck('name')->join(', ') }}
                            @else
                                <em>No categories</em>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('products.show', $product) }}" style="color: #007bff; text-decoration: none;">View</a>
                            | <a href="{{ route('products.edit', $product) }}" style="color: #007bff; text-decoration: none;">Edit</a>
                            | <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background-color: #dc3545; padding: 0; border: none; cursor: pointer; color: #007bff; text-decoration: none; font-size: inherit; font-family: inherit;" onclick="return confirm('Are you sure?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div>
            {{ $products->render('pagination::simple-bootstrap-4') }}
        </div>
    @else
        <div style="background-color: white; padding: 2rem; text-align: center; border-radius: 4px;">
            <p>No products found. <a href="{{ route('products.create') }}">Create one now</a>.</p>
        </div>
    @endif
@endsection
