@extends('layouts.app')

@section('styles')
<style>
    .profile-page { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 2rem 0; }
    .profile-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; background: white; }
    .profile-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15); }
    .profile-header { background: linear-gradient(135deg, #2E7D32 0%, #43A047 100%); padding: 2rem; text-align: center; position: relative; }
    .profile-header::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 50px; background: white; clip-path: polygon(0 50%, 100% 0, 100% 100%, 0% 100%); }
    .profile-avatar-large { width: 120px; height: 120px; background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%); color: #2E7D32; border: 5px solid white; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); font-size: 3rem; font-weight: 700; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; border-radius: 50%; position: relative; z-index: 1; }
    .profile-name { color: white; font-size: 1.75rem; font-weight: 600; margin-bottom: 0.5rem; position: relative; z-index: 1; }
    .profile-role-badge { display: inline-block; padding: 0.5rem 1.5rem; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 50px; color: white; font-weight: 500; text-transform: uppercase; font-size: 0.875rem; letter-spacing: 0.5px; position: relative; z-index: 1; }
    .profile-info-section { padding: 2rem; }
    .info-item { display: flex; align-items: center; padding: 1rem; margin-bottom: 0.75rem; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #2E7D32; transition: all 0.3s ease; }
    .info-item:hover { background: #e8f5e9; transform: translateX(5px); }
    .info-icon { width: 40px; height: 40px; background: linear-gradient(135deg, #2E7D32 0%, #43A047 100%); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 1.2rem; }
    .info-label { font-weight: 600; color: #555; margin-right: 0.5rem; font-size: 0.9rem; }
    .info-value { color: #333; flex: 1; }
    .section-divider { margin: 2rem 0; border: none; height: 2px; background: linear-gradient(90deg, transparent, #2E7D32, transparent); }
    .section-title { font-size: 1.5rem; font-weight: 600; color: #2E7D32; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .section-title i { width: 40px; height: 40px; background: linear-gradient(135deg, #2E7D32 0%, #43A047 100%); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .form-label { font-weight: 600; color: #555; margin-bottom: 0.5rem; }
    .form-control { border: 2px solid #e0e0e0; border-radius: 10px; padding: 0.75rem 1rem; transition: all 0.3s ease; }
    .form-control:focus { border-color: #2E7D32; box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.15); }
    .btn-professional { padding: 0.875rem 2rem; font-weight: 600; border-radius: 10px; border: none; transition: all 0.3s ease; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.875rem; }
    .btn-professional-primary { background: linear-gradient(135deg, #2E7D32 0%, #43A047 100%); color: white; box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3); }
    .btn-professional-primary:hover { background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); transform: translateY(-2px); }
    .btn-professional-success { background: linear-gradient(135deg, #388E3C 0%, #4CAF50 100%); color: white; box-shadow: 0 4px 15px rgba(56, 142, 60, 0.3); }
    .btn-professional-success:hover { background: linear-gradient(135deg, #2E7D32 0%, #388E3C 100%); box-shadow: 0 6px 20px rgba(56, 142, 60, 0.4); transform: translateY(-2px); }
    .alert-professional { border-radius: 10px; border: none; padding: 1rem 1.5rem; margin-bottom: 1.5rem; }
    .alert-professional.alert-success { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); color: #1B5E20; border-left: 4px solid #4CAF50; }
    .alert-professional.alert-danger { background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #b71c1c; border-left: 4px solid #f44336; }
    .form-section { background: #f8f9fa; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
</style>
@endsection

@section('content')
<div class="profile-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar-large">{{ strtoupper(substr(Auth::user()->full_name, 0, 1)) }}</div>
                        <h2 class="profile-name">{{ Auth::user()->full_name }}</h2>
                        <span class="profile-role-badge">{{ Auth::user()->role ?? 'User' }}</span>
                    </div>
                    <div class="profile-info-section">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-envelope"></i></div>
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ Auth::user()->email }}</span>
                        </div>
                        @if(Auth::user()->cin)
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-id-card"></i></div>
                            <span class="info-label">CIN:</span>
                            <span class="info-value">{{ Auth::user()->cin }}</span>
                        </div>
                        @endif
                        <hr class="section-divider">
                        <div class="section-title"><i class="fas fa-key"></i><span>Change Password</span></div>
                        @if (session('status'))
                            <div class="alert alert-professional alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-professional alert-danger"><i class="fas fa-exclamation-circle me-2"></i><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif
                        <form method="POST" action="{{ route('profile.password.update') }}" class="form-section">
                            @csrf
                            <div class="mb-3"><label for="current_password" class="form-label"><i class="fas fa-lock me-2 text-muted"></i>Current Password</label><input type="password" class="form-control" id="current_password" name="current_password" required></div>
                            <div class="form-row">
                                <div class="mb-3"><label for="password" class="form-label"><i class="fas fa-key me-2 text-muted"></i>New Password</label><input type="password" class="form-control" id="password" name="password" required></div>
                                <div class="mb-3"><label for="password_confirmation" class="form-label"><i class="fas fa-check-double me-2 text-muted"></i>Confirm New Password</label><input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required></div>
                            </div>
                            <button type="submit" class="btn btn-professional btn-professional-primary w-100"><i class="fas fa-save me-2"></i>Update Password</button>
                        </form>
                        @if (Auth::user() && Auth::user()->isAdministrator())
                            <div class="section-title"><i class="fas fa-user-plus"></i><span>Create Manager Account</span></div>
                            @if (session('manager_status'))
                                <div class="alert alert-professional alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('manager_status') }}</div>
                            @endif
                            @if ($errors->has('manager'))
                                <div class="alert alert-professional alert-danger"><i class="fas fa-exclamation-circle me-2"></i><ul class="mb-0 ps-3">@foreach ($errors->get('manager') as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                            @endif
                            <form method="POST" action="{{ route('profile.manager.create') }}" class="form-section">
                                @csrf
                                <div class="form-row">
                                    <div class="mb-3"><label for="manager_firstname" class="form-label"><i class="fas fa-user me-2 text-muted"></i>First Name</label><input type="text" class="form-control" id="manager_firstname" name="manager_firstname" required></div>
                                    <div class="mb-3"><label for="manager_lastname" class="form-label"><i class="fas fa-user me-2 text-muted"></i>Last Name</label><input type="text" class="form-control" id="manager_lastname" name="manager_lastname" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="mb-3"><label for="manager_email" class="form-label"><i class="fas fa-envelope me-2 text-muted"></i>Email</label><input type="email" class="form-control" id="manager_email" name="manager_email" required></div>
                                    <div class="mb-3"><label for="manager_cin" class="form-label"><i class="fas fa-id-card me-2 text-muted"></i>CIN</label><input type="text" class="form-control" id="manager_cin" name="manager_cin" required></div>
                                </div>
                                <div class="form-row">
                                    <div class="mb-3"><label for="manager_password" class="form-label"><i class="fas fa-lock me-2 text-muted"></i>Password</label><input type="password" class="form-control" id="manager_password" name="manager_password" required></div>
                                    <div class="mb-3"><label for="manager_password_confirmation" class="form-label"><i class="fas fa-check-double me-2 text-muted"></i>Confirm Password</label><input type="password" class="form-control" id="manager_password_confirmation" name="manager_password_confirmation" required></div>
                                </div>
                                <button type="submit" class="btn btn-professional btn-professional-success w-100"><i class="fas fa-user-plus me-2"></i>Create Manager Account</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
