@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0 page-title">Product Inventory</h1>
            <div>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="{{ route('products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Product
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search and Filter Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('products.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Search by product name..."
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <select name="category_id" class="form-select" aria-label="Filter by category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                @if(request('search') || request('category_id'))
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td class="text-end">
                                <span class="badge bg-{{ $product->current_quantity > 20 ? 'success' : 'warning' }}">
                                    {{ $product->current_quantity }} units
                                </span>
                                </td>
                                <td class="text-end">{{ number_format($product->price, 2) }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-info rounded d-flex align-items-center justify-content-center" title="View">
                                            <i class="fas fa-eye me-1"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning rounded d-flex align-items-center" title="Edit">
                                            <i class="fas fa-edit me-1"></i>
                                        </a>
                                        <a href="{{ route('products.add-stock.form', $product) }}" class="btn btn-success rounded d-flex align-items-center" title="Add Stock">
                                            <i class="fas fa-plus me-1"></i>
                                        </a>
                                        <a href="{{ route('products.use-stock.form', $product) }}" class="btn btn-secondary rounded d-flex align-items-center" title="Use Stock">
                                            <i class="fas fa-minus me-1"></i>
                                        </a>
                                        <a href="{{ route('transactions.product-usage-report', ['product_id' => $product->id]) }}" class="btn btn-primary rounded d-flex align-items-center" title="Usage Report">
                                            <i class="fas fa-chart-line me-1"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger rounded d-flex align-items-center" title="Delete" onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash-alt me-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if(request('search'))
                                        No products found matching "{{ request('search') }}"
                                    @else
                                        No products found
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                        </div>
                        <div>
                            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .btn-rounded-pill {
            border-radius: 50rem!important;
            font-size: 0.95em;
            transition: box-shadow 0.15s;
        }
        .btn-group .btn {
            margin-right: 0.1rem;
        }
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        .table th {
            white-space: nowrap;
        }
        .badge {
            font-size: 0.85em;
            min-width: 70px;
        }
        .input-group {
            max-width: 500px;
        }
    </style>
@endsection
