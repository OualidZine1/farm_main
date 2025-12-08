@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Field Management</h1>
            <p class="mb-0">Manage your fields and their information</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('fields.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Field
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold">All Fields</h6>
        </div>
        <div class="card-body">
            @if($fields->isEmpty())
                <div class="alert alert-info">
                    No fields found. Create your first field by clicking the "Add New Field" button.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Block Number</th>
                                <th>Crop Type</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fields as $field)
                                <tr>
                                    <td>{{ $field->bloc_number }}</td>
                                    <td>{{ $field->crop_type }}</td>
                                    <td>{{ $field->location }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('fields.edit', $field) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('fields.destroy', $field) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this field? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
