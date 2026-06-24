@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0 page-title">Categories</h1>
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Category
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('categories.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search by category name..."
                               value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        @endif
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
                            <th>Category Name</th>
                            <th>Products Count</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->products->count() }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
    @include('categories.partials.action-buttons', ['category' => $category])

                                        <!-- Edit Button -->
                                        
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No categories found
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($categories->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} entries
                        </div>
                        <div>
                            {{ $categories->links('pagination::bootstrap-5') }}
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
    </style>
@endsection
