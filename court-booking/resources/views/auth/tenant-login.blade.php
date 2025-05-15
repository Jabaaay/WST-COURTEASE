<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tenant Login</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <!-- Poppins Font -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <!-- reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <body>
        <div class="container">
            <div class="row justify-content-center min-vh-100 align-items-center">

            
                <div class="col-md-6 col-lg-4">

                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{ $errors->first() }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                </div>
                            @endif
                    <div class="card shadow rounded-3">
                        <div class="card-body p-4 bg-dark">
                            <div class="text-center d-flex justify-content-center">
                                <img src="{{ asset('assets/img/COURT.png') }}" alt="" style="width: 150px; height: 100px;">
                            </div>

                        

                            <form method="POST" action="{{ route('tenant.login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label  for="email" class="form-label text-white text-sm">Email</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        class="form-control bg-dark text-white">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label text-white text-sm">Password</label>
                                    <input id="password" type="password" name="password" required
                                        class="form-control bg-dark text-white">
                                </div>

                                <div class="mb-3">
                                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                                    @error('g-recaptcha-response')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <a href="{{ route('auth.tenant-register-user') }}" class="text-decoration-none text-white text-sm">Don't have an account? <span class="text-warning">Register</span></a>
                                </div>

                                <div class="d-grid mt-3 bg-outline-primary">
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-0">
                                        <i class="bi bi-box-arrow-in-right text-white"></i>
                                        Log in
                                    </button>
                                </div>

                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html> 