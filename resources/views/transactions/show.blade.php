@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Transaction Details</h1>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Transaction #{{ $transaction->id }}</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Date:</strong> {{ $transaction->date }}</li>
                <li class="list-group-item"><strong>Type:</strong> {{ ucfirst($transaction->type) }}</li>
                <li class="list-group-item"><strong>Product:</strong> {{ $transaction->product->name ?? '-' }}</li>
                <li class="list-group-item"><strong>Field:</strong> {{ $transaction->field->bloc_number ?? '-' }}</li>
                <li class="list-group-item"><strong>Quantity:</strong> {{ $transaction->quantity }}</li>
                <li class="list-group-item"><strong>Price:</strong> {{ $transaction->price }}</li>
                <li class="list-group-item"><strong>Entered By:</strong> {{ $transaction->enteredBy->name ?? '-' }}</li>
                <li class="list-group-item"><strong>Used By:</strong> {{ $transaction->usedBy->name ?? '-' }}</li>
                <li class="list-group-item"><strong>Notes:</strong> {{ $transaction->notes ?? '-' }}</li>
            </ul>
        </div>
    </div>
    <a href="{{ route('transactions.index') }}" class="btn btn-secondary mt-3">Back to Transactions</a>
</div>
@endsection
