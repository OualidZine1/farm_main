@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Transaction History for {{ $product->name }}</h4>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Back to Products</a>
                </div>
                <div class="card-body">
                    @if($transactions->isEmpty())
                        <p class="text-muted">No transactions found for this product.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
<th>Type</th>
<th>Quantity</th>
<th>Field</th>
<th>Used By</th>
<th>Recorded By</th>
<th>Price at Transaction</th>
<th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
    
                                    <tr>
                                        <td>{{ $transaction->date }}</td>
                                        <td>
                                            @if($transaction->type === 'in')
                                                <span class="badge bg-success">Add Stock</span>
                                            @else
                                                <span class="badge bg-danger">Out Stock</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->quantity }}</td>
                                        <td>{{ optional($transaction->field)->name ?? '-' }}</td>
                                        <td>{{ optional($transaction->usedBy)->name ?? '-' }}</td>
                                        <td>{{ optional($transaction->enteredBy)->full_name ?? optional($transaction->enteredBy)->name ?? optional($transaction->enteredBy)->email ?? '-' }}</td>
                                        <td>{{ number_format($transaction->price, 2) }}</td>
<td>{{ $transaction->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
