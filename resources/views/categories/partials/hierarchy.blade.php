<div class="tree-item">
    @if ($level > 0)
        <span style="margin-left: {{ ($level - 1) * 1.5 }}rem;">└─</span>
    @endif
    <a href="{{ route('categories.show', $category['id']) }}">
        {{ $category['name'] }}
    </a>
    <span class="text-muted small">({{ count($category['children']) }} children)</span>
</div>

@foreach ($category['children'] as $child)
    @include('categories.partials.hierarchy', ['category' => $child, 'level' => $level + 1])
@endforeach
