<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connectez-vous au système d'inventaire agricole pour gérer vos produits et ressources">
    <title>{{ config('app.name') }} - Connexion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
<section class="auth-shell">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-10 col-md-5 col-lg-6 col-xl-5">
                <div class="card auth-card">
                    <div class="row g-0 justify-content-center">
                        <!-- Right Side - Form -->
                        <div class="col-lg-12 d-flex align-items-center">
                            <div class="card-body p-1 p-lg-3">
                                <!-- Logo/Header -->
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-tractor fa-2x me-3 custom-primary" aria-hidden="true"></i>
                                    <h1 class="h2 fw-bold mb-0">{{ config('app.name') }}</h1>
                                </div>

                                <h2 class="h5 fw-normal mb-4" style="letter-spacing: 1px;">Connectez-vous à votre compte</h2>

                                @if(session('status'))
                                    <div class="alert alert-success mb-4" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <!-- Login Form -->
                                <form method="POST" action="{{ route('login') }}" aria-label="Formulaire de connexion">
                                    @csrf

                                    <!-- Email Input -->
                                    <div class="mb-4">
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               placeholder="Adresse Email"
                                               class="form-control form-control-lg @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               required
                                               autocomplete="email"
                                               autofocus
                                               aria-describedby="emailHelp">
                            
                                        @error('email')
                                        <div class="invalid-feedback" id="emailHelp">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Password Input -->
                                    <div class="mb-4 position-relative">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               placeholder="Mot de passe"
                                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                                               required
                                               autocomplete="current-password"
                                               aria-describedby="passwordHelp">
                                            
                                        <span class="password-toggle" onclick="togglePasswordVisibility()" aria-hidden="true">
                                            <i class="far fa-eye" id="togglePassword"></i>
                                        </span>
                                        @error('password')
                                        <div class="invalid-feedback" id="passwordHelp">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="form-check mb-4">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="remember"
                                               id="remember"
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-1 mb-4">
                                        <button type="submit" class="btn btn-login btn-lg w-100 text-white">
                                            <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i> Connexion
                                        </button>
                                    </div>
                                    <a href="{{ route('password.request') }}">Forgot your password?</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>
