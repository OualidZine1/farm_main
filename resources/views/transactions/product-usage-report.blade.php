@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0 page-title">Product Usage Report</h1>
            <p class="mb-0">Track how products are being used across different fields and by different users</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                <i class="fas fa-list me-1"></i> All Transactions
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Filter Report</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('transactions.product-usage-report') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select class="form-select" id="product_id" name="product_id">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="field_id" class="form-label">Field</label>
                        <select class="form-select" id="field_id" name="field_id">
                            <option value="">All Fields</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}" {{ request('field_id') == $field->id ? 'selected' : '' }}>
                                    {{ $field->bloc_number }} ({{ $field->crop_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="used_by_user_id" class="form-label">Used By</label>
                        <select class="form-select" id="used_by_user_id" name="used_by_user_id">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('used_by_user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('transactions.product-usage-report') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">Usage Summary</h6>
            <div>
                <a href="{{ route('transactions.product-usage-report', array_merge(request()->all(), ['export' => true])) }}" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-file-excel me-1"></i> Export to Excel
                </a>
                <button class="btn btn-sm btn-primary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Report
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($usageByProductFieldUser->isEmpty())
                <div class="alert alert-info">
                    No usage data found for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Field</th>
                                <th>Used By</th>
                                <th class="text-end">Total Quantity</th>
                                <th class="text-end">Usage Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageByProductFieldUser as $usage)
                                <tr>
                                    <td>{{ $usage->product->name }}</td>
                                    <td>{{ $usage->field ? $usage->field->bloc_number . ' (' . $usage->field->crop_type . ')' : 'N/A' }}</td>
                                    <td>{{ $usage->usedBy ? $usage->usedBy->full_name : 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($usage->total_quantity, 2) }}</td>
                                    <td class="text-end">{{ $usage->usage_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Detailed Transactions Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Detailed Transactions</h6>
        </div>
        <div class="card-body">
            @if($transactions->isEmpty())
                <div class="alert alert-info">
                    No transactions found for the selected filters.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                                <th>Field</th>
                                <th>Used By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->date->format('Y-m-d') }}</td>
                                    <td>{{ $transaction->product->name }}</td>
                                    <td class="text-end">{{ $transaction->quantity }}</td>
                                    <td>{{ $transaction->field ? $transaction->field->bloc_number . ' (' . $transaction->field->crop_type . ')' : 'N/A' }}</td>
                                    <td>{{ $transaction->usedBy ? $transaction->usedBy->full_name : 'N/A' }}</td>
                                    <td>{{ $transaction->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Page Total Quantity:</th>
                                <th class="text-end">{{ number_format($transactions->sum('quantity'), 2) }}</th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
                
                <!-- Grand Totals -->
                <div class="row mt-4">
                    <div class="col-md-4 offset-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th>Grand Total Quantity:</th>
                                <td class="text-end">{{ number_format($grandTotalQuantity, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
