@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Use Stock: {{ $product->name }}</h4>
                            <a href="{{ route('products.index') }}" class="btn btn-dark btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('products.use-stock', $product) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="current_quantity" class="form-label">Current Stock</label>
                                <input type="text" class="form-control" value="{{ $product->current_quantity }} units" readonly>
                                @if($product->current_quantity <= 10)
                                    <small class="text-danger">Low stock warning!</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity to Use <span class="text-danger">*</span></label>
                                <input type="number"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity"
                                       name="quantity"
                                       min="1"
                                       max="{{ $product->current_quantity }}"
                                       value="{{ old('quantity') }}"
                                       required>
                                @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="field_id" class="form-label">Field/Block <span class="text-danger">*</span></label>
                                <select class="form-select @error('field_id') is-invalid @enderror"
                                        id="field_id"
                                        name="field_id"
                                        required>
                                    <option value="">Select Field</option>
                                    @foreach($fields as $field)
                                        <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : '' }}>
                                            {{ $field->bloc_number }} - {{ $field->crop_type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('field_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="mb-3">
                                <label for="date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control @error('date') is-invalid @enderror"
                                       id="date"
                                       name="date"
                                       value="{{ old('date', now()->format('Y-m-d')) }}"
                                       required>
                                @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="used_by_user_id" class="form-label">Used By</label>
                                <select class="form-select" id="used_by_user_id" name="used_by_user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Usage Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning text-white">
                                    <i class="fas fa-check-circle me-1"></i> Confirm Usage
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
