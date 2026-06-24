<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Inventory System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                @if(auth()->user()?->isAdministrator())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                            <i class="fas fa-clock-rotate-left me-2"></i> Audit Trail
                        </a>
                    </li>
                @endif
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

@yield('scripts')
</body>
</html>
