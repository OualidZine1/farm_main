@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Product</h1>
        <form action="{{ route('products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $product->name }}" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <div class="form-label">Current Quantity</div>
                <div class="form-control-plaintext">
                    {{ $product->current_quantity }} units
                    <div class="form-text">Manage stock levels using the 'Add Stock' or 'Use Stock' options.</div>
                </div>
            </div>
            <div class="mb-3">
                <label for="min_quantity" class="form-label">Low Stock Alert Quantity</label>
                <input type="number" class="form-control" id="min_quantity" name="min_quantity" min="0" value="{{ old('min_quantity', $product->min_quantity) }}" required>
            </div>
            <div class="mb-3">
                <div class="form-label">Average Price</div>
                <div class="form-control-plaintext">
                    ${{ number_format($product->average_price, 2) }}
                    <div class="form-text">Based on weighted average of all stock transactions.</div>
                </div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $product->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
@endsection
