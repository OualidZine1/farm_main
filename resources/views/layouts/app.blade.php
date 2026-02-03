<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Inventory System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background:#2E7D32;
            color: white;
        }
        .btn{
            margin: 1px;
            border-radius: 10px;
        }
        .btn + .btn {
            margin-left: -1px; 
        }
        .btn-group {
            display: inline-flex;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover {
            color: black;
            background:#43A047;
        }
        .sidebar .nav-link.active {
            color: #43A047;
            background: #FFFFFF;
        }
        .main-content {
            padding: 20px;
        }
        .page-title {
            min-height: 56px;
            display: flex;
            align-items: center;
            font-size: 2rem;
            font-weight: 600;
        }
        .navbar-nav .nav-link {
            color: white;
        }
        .navbar-nav .nav-link:hover {
            color: #43A047;
        }
        .navbar {
            color: white;
            background: #4CAF50 !important;
        }
        .navbar .navbar-brand,
        .navbar .navbar-brand i {
            color: #fff !important;
        }
        
        /* Flash Message Styling */
        .alert {
            border-radius: 8px;
            margin: 1rem 0;
            padding: 1rem;
            border: 1px solid transparent;
            position: relative;
            padding-right: 2.5rem;
        }
        
        .alert.alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .alert.alert-error .close {
            color: #721c24;
            opacity: 0.8;
        }
        
        .alert .close {
            position: absolute;
            top: 0.75rem;
            right: 1rem;
            font-size: 1.25rem;
            line-height: 1;
            background: transparent;
            border: none;
            cursor: pointer;
        }
        
    </style>
    @yield('styles')
</head>
<body>
<!-- Navbar Without Auth -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <i class="fas fa-tractor me-2"></i> Farm Inventory
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="fas fa-boxes me-1"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('fields.*') ? 'active' : '' }}" href="{{ route('fields.index') }}">
                        <i class="fas fa-map-marked-alt me-1"></i> Fields
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-alt me-1"></i> Reports
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="reportsDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('transactions.dashboard') }}">
                                <i class="fas fa-chart-bar me-1"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transactions.product-usage-report') }}">
                                <i class="fas fa-chart-line me-1"></i> Product Usage Report
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('transactions.index') }}">
                                <i class="fas fa-list me-1"></i> All Transactions
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                        <i class="fas fa-user-circle me-1"></i> Profile
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 d-none d-md-block sidebar p-0 d-flex flex-column" style="min-height: 100vh;">
            <div class="p-3">
                <h5 class="text-center">Menu</h5>
            </div>
            <ul class="nav flex-column flex-grow-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-2"></i> Home
                    </a>
                </li>
                @auth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                        <i class="fas fa-user-circle me-2"></i> Profile
                    </a>
                </li>
                @endauth
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="fas fa-boxes me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('fields.*') ? 'active' : '' }}" href="{{ route('fields.index') }}">
                        <i class="fas fa-map-marked-alt me-2"></i> Fields
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transactions.dashboard') ? 'active' : '' }}" href="{{ route('transactions.dashboard') }}">
                        <i class="fas fa-chart-bar me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transactions.product-usage-report') ? 'active' : '' }}" href="{{ route('transactions.product-usage-report') }}">
                        <i class="fas fa-chart-line me-2"></i> Product Usage Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                        <i class="fas fa-list me-2"></i> Transactions
                    </a>
                </li>
            @auth
                <li class="nav-item">
                    <a href="#" class="nav-link"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            @endauth
            </ul>
        </div>

        <!-- Page Content -->
        <div class="col-md-10 main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')
</body>
</html>
