@extends('layouts.app')

@push('styles')
<style>
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .card {
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-top: none;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .text-success {
        color: #198754 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .bg-success {
        background-color: #198754 !important;
    }
    
    .bg-danger {
        background-color: #dc3545 !important;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="mb-0 page-title">Inventory Dashboard</h1>
                <p class="text-muted">Overview of stock movements and usage patterns</p>
            </div>
        </div>

        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-md-3">
                <form method="GET" action="{{ route('transactions.dashboard') }}" class="d-flex align-items-end">
                    <div class="me-2">
                        <label for="months" class="form-label">Months</label>
                        <select name="months" id="months" class="form-select">
                            <option value="6" {{ ($selectedMonths ?? 6) == 6 ? 'selected' : '' }}>Last 6 Months</option>
                            <option value="12" {{ ($selectedMonths ?? 6) == 12 ? 'selected' : '' }}>Last 12 Months</option>
                        </select>
                    </div>
                    <div class="me-2">
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select">
                            @for($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ ($selectedYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Apply</button>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-primary">Total Stock Added</h5>
                                <h2 class="mb-0">{{ number_format($totalIn) }}</h2>
                            </div>
                            <div class="icon-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                        </div>
                        <p class="text-muted mb-0">Last 6 months</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-danger">Total Stock Used</h5>
                                <h2 class="mb-0">{{ number_format($totalOut) }}</h2>
                            </div>
                            <div class="icon-circle bg-danger text-white d-flex align-items-center justify-content-center">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        <p class="text-muted mb-0">Last 6 months</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title text-success">Current Balance</h5>
                                <h2 class="mb-0">{{ number_format($currentBalance) }}</h2>
                            </div>
                            <div class="icon-circle bg-success text-white d-flex align-items-center justify-content-center">
                                <i class="fas fa-boxes"></i>
                            </div>
                        </div>
                        <p class="text-muted mb-0">Current stock level</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Inventory Flow (Last 6 Months)</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 400px; width: 100%;">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Transactions</h5>
                        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Product</th>
                                    <th>Field</th>
                                    <th>Quantity</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->date->format('M d, Y') }}</td>
                                        <td>
                                            @if($transaction->type === 'in')
                                                <span class="badge bg-success">IN</span>
                                            @else
                                                <span class="badge bg-danger">OUT</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->product->name }}</td>
                                        <td>{{ $transaction->field->bloc_number ?? 'N/A' }}</td>
                                        <td class="{{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                        </td>
                                        <td>{{ $transaction->user->full_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No recent transactions</td>
                                    </tr>
                                @endforelse
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
            console.log('Initializing chart...');
            
            const canvas = document.getElementById('inventoryChart');
            if (!canvas) {
                console.error('Canvas element not found!');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('Could not get 2D context');
                return;
            }
            
            // Get the chart data from the controller
            const chartData = @json($chartData);
            
            try {
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets.map(dataset => ({
                            ...dataset,
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
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toLocaleString();
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Quantity'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            }
                        }
                    }
                });
                
                console.log('Chart initialized successfully');
            } catch (error) {
                console.error('Error initializing chart:', error);
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        .table-responsive {
            min-height: 200px;
        }
    </style>
@endsection
