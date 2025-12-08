@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <i class="fas fa-user-circle fa-lg me-2"></i>
        <span class="fw-bold">Profile</span>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <div class="profile-avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width:48px; height:48px; font-size:1.5rem;">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
            <div>
                <span class="fw-bold fs-5">{{ Auth::user()->name }}</span><br>
                <span class="badge bg-info text-dark text-capitalize">{{ Auth::user()->role ?? 'N/A' }}</span>
            </div>
        </div>
        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item px-0 border-0">
                <i class="fas fa-envelope me-2 text-primary"></i>
                <strong>Email:</strong> {{ Auth::user()->email }}
            </li>
            @if(Auth::user()->cin)
            <li class="list-group-item px-0 border-0">
                <i class="fas fa-id-card me-2 text-primary"></i>
                <strong>CIN:</strong> {{ Auth::user()->cin }}
            </li>
            @endif
        </ul>
        <hr>
        <h5 class="mb-3"><i class="fas fa-key me-2 text-warning"></i>Change Password</h5>
@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="{{ route('profile.password.update') }}">
    @csrf
    <div class="mb-3">
        <label for="current_password" class="form-label">Current Password</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save me-1"></i> Change Password</button>
</form>
<hr class="my-4">
@if (Auth::user() && Auth::user()->isAdministrator())
    <h5 class="mb-3 text-success"><i class="fas fa-user-plus me-2"></i>Create Manager Account</h5>
    @if (session('manager_status'))
        <div class="alert alert-success">{{ session('manager_status') }}</div>
    @endif
    @if ($errors->has('manager'))
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->get('manager') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('profile.manager.create') }}">
        @csrf
        <div class="mb-3">
            <label for="manager_firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="manager_firstname" name="manager_firstname" required>
        </div>
        <div class="mb-3">
            <label for="manager_lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="manager_lastname" name="manager_lastname" required>
        </div>
        <div class="mb-3">
            <label for="manager_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="manager_email" name="manager_email" required>
        </div>
        <div class="mb-3">
            <label for="manager_cin" class="form-label">CIN</label>
            <input type="text" class="form-control" id="manager_cin" name="manager_cin" required>
        </div>
        <div class="mb-3">
            <label for="manager_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="manager_password" name="manager_password" required>
        </div>
        <div class="mb-3">
            <label for="manager_password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="manager_password_confirmation" name="manager_password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-success w-100"><i class="fas fa-user-plus me-1"></i> Create Manager</button>
    </form>
@endif
        </div>
    </div>
</div>
@endsection
