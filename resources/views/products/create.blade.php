@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Product</h1>
        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="current_quantity" class="form-label">Initial Quantity (optional)</label>
                <input type="number" class="form-control" id="current_quantity" name="current_quantity" min="0" value="{{ old('current_quantity', 0) }}">
                <div class="form-text">Enter the initial stock quantity. If greater than 0, you must specify a price per unit below.</div>
            </div>
            <div class="mb-3">
                <label for="min_quantity" class="form-label">Low Stock Alert Quantity</label>
                <input type="number" class="form-control" id="min_quantity" name="min_quantity" min="0" value="{{ old('min_quantity', 10) }}" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price per Unit (DH)</label>
                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="{{ old('price', 0) }}">
                <div class="form-text">Required if initial quantity is greater than 0.</div>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Product</button>
        </form>
    </div>
@endsection
