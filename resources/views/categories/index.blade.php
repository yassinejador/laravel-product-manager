@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Categories</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                + Add Category
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($categories->isEmpty())
        <div class="alert alert-info">
            No categories yet. <a href="{{ route('categories.create') }}">Create one now</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>
                                <a href="{{ route('categories.show', $category) }}">
                                    {{ $category->name }}
                                </a>
                            </td>
                            <td>
                                @if ($category->parent)
                                    <span class="badge bg-secondary">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $category->products->count() }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr class="my-5">

        <h2>Category Hierarchy</h2>
        <div class="category-tree">
            @foreach ($hierarchyData as $root)
                @include('categories.partials.hierarchy', ['category' => $root, 'level' => 0])
            @endforeach
        </div>
    @endif
</div>

<style>
    .category-tree {
        font-size: 0.95rem;
    }
    .tree-item {
        margin-left: 1.5rem;
        padding: 0.5rem 0;
    }
    .tree-item > span {
        padding-left: 0.5rem;
    }
</style>
@endsection
