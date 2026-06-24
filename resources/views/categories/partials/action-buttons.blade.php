{{-- Category Action Buttons Partial --}}
<div class="btn-group" role="group">
    <a href="{{ route('categories.edit', $category->id ?? 0) }}" class="btn btn-sm btn-primary">Edit</a>
    <form action="{{ route('categories.destroy', $category->id ?? 0) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
    </form>
</div>
