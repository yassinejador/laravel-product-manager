@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2>{{ $product->name }}</h2>
        <div>
            <a href="{{ route('products.edit', $product) }}" class="btn">Edit</a>
            <form method="POST" action="{{ route('products.destroy', $product) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background-color: #dc3545;" onclick="return confirm('Are you sure?');">Delete</button>
            </form>
        </div>
    </div>

    <div style="background-color: white; padding: 2rem; border-radius: 4px; max-width: 800px;">
        @if ($product->image)
            <div style="margin-bottom: 2rem;">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100%; height: auto; border-radius: 4px;">
            </div>
        @endif

        <div style="margin-bottom: 1.5rem;">
            <h4 style="margin-bottom: 0.5rem;">Price</h4>
            <p style="font-size: 1.5rem; font-weight: bold;">${{ number_format($product->price, 2) }}</p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <h4 style="margin-bottom: 0.5rem;">Description</h4>
            <p>{{ $product->description }}</p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <h4 style="margin-bottom: 0.5rem;">Categories</h4>
            @if ($product->categories->count() > 0)
                <ul style="margin-left: 2rem;">
                    @foreach ($product->categories as $category)
                        <li>{{ $category->name }}</li>
                    @endforeach
                </ul>
            @else
                <p><em>No categories assigned</em></p>
            @endif
        </div>

        <div style="margin-top: 2rem;">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>
@endsection
