<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CourtEase - Court Booking System</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <style>
        body
        {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <body class="bg-light">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold text-primary" href="#">CourtEase</a>
                <div class="d-flex">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary me-2">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="hero" class="py-5 bg-primary text-white">
            <div class="container">
                <div class="text-center py-5">
                    <h1 class="display-4 fw-bold mb-4">
                        Welcome to CourtEase
                        <span class="d-block text-light-emphasis">Your Smart Court Booking Solution</span>
                    </h1>
                    <p class="lead mb-4">
                        Streamline your court booking process with our easy-to-use platform. Manage reservations, track availability, and enhance your facility's efficiency.
                    </p>
                    <a href="#register" class="btn btn-light btn-lg">Get Started</a>
                </div>
            </div>
        </section>

        <!-- Registration Form Section -->
        <section id="register" class="py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body p-4">
                                <h2 class="text-center mb-4">Tenant Registration</h2>
                                @if(session('success'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <form method="POST" action="{{ route('tenant.register') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="domain" class="form-label">Domain</label>
                                        <input type="text" class="form-control" id="domain" name="domain" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Submit Application</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Plans Section -->
        <section id="pricing" class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold">Choose Your Plan</h2>
                    <p class="lead text-muted">Select the perfect plan for your facility's needs</p>
                </div>

                <div class="row g-4">
                    <!-- Basic Plan -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-primary">
                            <div class="card-body p-4">
                                <h3 class="card-title h5">Basic</h3>
                                <p class="text-muted">Perfect for small facilities</p>
                                <h4 class="display-6 fw-bold mb-4">$9.99<small class="text-muted fs-6">/month</small></h4>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Book at Regular Hours (8AM - 5PM)</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 2 Bookings Per Week</li>
                                    <li class="mb-2"><i class="bi bi-x-circle-fill text-danger me-2"></i>No Rescheduling</li>
                                    <li class="mb-2"><i class="bi bi-x-circle-fill text-danger me-2"></i>Can't Book Weekends</li>
                                </ul>
                                <button onclick="openModal('basic')" class="btn btn-primary w-100">Get Started</button>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Plan -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body p-4">
                                <h3 class="card-title h5">Premium</h3>
                                <p class="text-muted">Ideal for medium facilities</p>
                                <h4 class="display-6 fw-bold mb-4">$19.99<small class="text-muted fs-6">/month</small></h4>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Can Book Weekends</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 4 Bookings Per Week</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>1 Reschedule Allowed per Booking</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Can Book Up to 2 Weeks in Advance</li>
                                </ul>
                                <button onclick="openModal('premium')" class="btn btn-primary w-100">Get Started</button>
                            </div>
                        </div>
                    </div>

                    <!-- Ultimate Plan -->
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body p-4">
                                <h3 class="card-title h5">Ultimate</h3>
                                <p class="text-muted">For large facilities</p>
                                <h4 class="display-6 fw-bold mb-4">$49.99<small class="text-muted fs-6">/month</small></h4>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Full Access</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited Bookings</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Book Up to 1 month in advance</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Priority Slot Access</li>
                                </ul>
                                <button onclick="openModal('ultimate')" class="btn btn-primary w-100">Get Started</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Registration Modal -->
        <div class="modal fade" id="registrationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Register for <span id="planName" class="fw-bold"></span> Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('tenant.register') }}">
                            @csrf
                            <input type="hidden" name="plan" id="selectedPlan">
                            <div class="mb-3">
                                <label for="modal-name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="modal-name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="modal-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="modal-email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="modal-domain" class="form-label">Domain</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="modal-domain" name="domain" required placeholder="yourfacility">
                                    <span class="input-group-text">.localhost</span>
                                </div>
                                <div class="form-text">This will be your unique subdomain</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Submit Application</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-dark text-light py-4">
            <div class="container text-center">
                <p class="mb-0">&copy; 2024 CourtEase. All rights reserved.</p>
            </div>
        </footer>

        <!-- Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        
        <!-- JavaScript for Modal -->
        <script>
            function openModal(plan) {
                const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
                document.getElementById('planName').textContent = plan.charAt(0).toUpperCase() + plan.slice(1);
                document.getElementById('selectedPlan').value = plan;
                modal.show();
            }
        </script>
    </body>
</html>
