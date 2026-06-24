@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-0">Home</h1>
            <p class="text-muted">Welcome to your farm inventory dashboard</p>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Products</div>
                    <div class="display-6">{{ number_format($totalProducts) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Low Stock Alerts</div>
                    <div class="display-6 {{ $lowStockCount > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($lowStockCount) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted">Estimated Inventory Value</div>
                    <div class="display-6">{{ number_format($inventoryValue, 2) }} DH</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Most Used Products</div>
                <ul class="list-group list-group-flush">
                    @foreach($topProducts as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->product->name ?? '-' }}
                            <span class="badge bg-primary rounded-pill">{{ $item->total_used }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Stock Movement (Last 6 Months)</div>
                <div class="card-body">
                    <canvas id="miniInventoryChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Transactions</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($tx->date)->format('M d, Y') }}</td>
                                    <td>{{ $tx->product->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tx->type == 'in' ? 'success' : 'danger' }}">
                                            {{ $tx->type == 'in' ? 'IN' : 'OUT' }}
                                        </span>
                                    </td>
                                    <td>{{ $tx->quantity }}</td>
                                    <td>{{ $tx->user->firstname ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('miniInventoryChart').getContext('2d');
    const chartData = @json($chartData);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets.map(ds => ({
                ...ds,
                borderWidth: 1,
                borderRadius: 4,
                barThickness: 'flex',
                maxBarThickness: 30,
                minBarLength: 2,
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantity' }
                },
                x: {
                    title: { display: true, text: 'Month' }
                }
            }
        }
    });
});
</script>
@endsection
