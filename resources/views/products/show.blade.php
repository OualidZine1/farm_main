@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Product Details</h2>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Name:</div>
                    <div class="col-md-8">{{ $product->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Category:</div>
                    <div class="col-md-8">{{ $product->category->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Quantity:</div>
                    <div class="col-md-8">{{ $product->current_quantity }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Price:</div>
                    <div class="col-md-8">{{ number_format($product->price, 2) }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Description:</div>
                    <div class="col-md-8">{{ $product->description ?? 'N/A' }}</div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('transactions.product-usage-report', ['product_id' => $product->id]) }}" class="btn btn-info">
                        <i class="fas fa-chart-line"></i> Usage Report
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
