@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Add New Field</h1>
            <p class="mb-0">Create a new field in the system</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('fields.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Fields
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Field Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('fields.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="bloc_number" class="form-label">Block Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('bloc_number') is-invalid @enderror"
                           id="bloc_number" name="bloc_number" value="{{ old('bloc_number') }}" required>
                    @error('bloc_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter the block number or identifier for this field</div>
                </div>

                <div class="mb-3">
                    <label for="crop_type" class="form-label">Crop Type <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('crop_type') is-invalid @enderror"
                           id="crop_type" name="crop_type" value="{{ old('crop_type') }}" required>
                    @error('crop_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter the type of crop grown in this field</div>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                           id="location" name="location" value="{{ old('location') }}" required>
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter the location or address of this field</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
