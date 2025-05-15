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
        .full-screen-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        #hero {
            background-color: var(--bs-primary); /* Assuming bs-primary is your main theme color */
            color: white;
            position: relative;
            overflow: hidden;
        }
        .dark-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* adjust 0.5 for more/less darkness */
            z-index: 1;
            pointer-events: none;
        }
        #hero .container {
            position: relative;
            z-index: 2;
        }
        #register, #pricing, #team {
            background-color: var(--bs-light); /* Or another appropriate background */
        }
        .team-member img {
            width: 100%;
            max-width: 320px;
            height: auto;
            aspect-ratio: 4/5;
            object-fit: cover;
            background-color: darkgray;
            border-radius: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .team-member img:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }


        .pricing-card:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .bg-court {
            background-image: url('{{ asset('assets/img/covered1.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-linear-gradient(to right, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
        }
        
    </style>
    <body class="bg-dark">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-dark shadow-sm py-0">
            <div class="container">
                <img src="{{ asset('assets/img/COURT.png') }}" alt="CourtEase Logo" style="width: 100px; height: 70px;">
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
        <section id="hero" class="py-5 bg-primary text-white full-screen-section bg-court position-relative">
            <div class="dark-overlay"></div>
            <div class="container position-relative" style="z-index: 2;">
                <div class="text-center py-5">
                    <h1 class="display-4 fw-bold mb-4">
                        Welcome to CourtEase
                        <span class="d-block text-light-emphasis">Your Smart Court Booking Solution</span>
                    </h1>
                    <p class="lead mb-4">
                        Streamline your court booking process with our easy-to-use platform. Manage reservations, track availability, and enhance your facility's efficiency.
                    </p>
                    <a href="#register" class="btn btn-outline-light btn-lg">Get Started</a>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="about-us" class="py-5 bg-secondary text-white full-screen-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold">About CourtEase</h2>
                    <p class="lead text-muted">Learn more about our mission and vision.</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <p class="mb-4">
                            CourtEase is dedicated to revolutionizing the way sports facilities manage their bookings. Our platform is designed with both facility owners and users in mind, offering a seamless, efficient, and user-friendly experience.
                        </p>
                        <p class="mb-4">
                            Our mission is to simplify court reservations, reduce administrative overhead, and help facilities maximize their utilization. We believe in leveraging technology to make sports more accessible and enjoyable for everyone.
                        </p>
                        <p>
                            Whether you're a small community club or a large sports complex, CourtEase provides the tools you need to streamline your operations and delight your customers.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Registration Form Section -->
        <section id="register" class="py-5 bg-dark full-screen-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold text-white">Register for CourtEase</h2>
                </div>
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
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-0">Submit Application</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Plans Section -->
        <section id="pricing" class="py-5 bg-primary text-white full-screen-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold">Choose Your Plan</h2>
                    <p class="lead text-muted">Select the perfect plan for your facility's needs</p>
                </div>

                <div class="row g-4 justify-content-center">
                    <!-- Basic Plan -->
                    <div class="col-md-4 pricing-card">
                        <div class="card h-100 shadow-sm border-primary">
                            <div class="card-body p-4">
                                <h3 class="card-title h5">Basic</h3>
                                <p class="text-muted">Perfect for small facilities</p>
                                <h4 class="display-6 fw-bold mb-4">Free</h4>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Book 24/7</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 2 Bookings Per Week</li>
                                    <li class="mb-2"><i class="bi bi-x-circle-fill text-danger me-2"></i>Can't Book Weekends</li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <!-- Ultimate Plan -->
                    <div class="col-md-4 pricing-card">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section id="team" class="py-5 bg-secondary text-white full-screen-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold">Meet Our Team</h2>
                    <p class="lead">The people behind CourtEase</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <!-- Team Member 1 -->
                    <div class="col-12 col-sm-6 col-md-3 text-center team-member d-flex flex-column align-items-center" data-aos="fade-up" data-aos-delay="100">
                        <img src="{{ asset('assets/img/van.jpg') }}" alt="Team Member 1" class="mb-3">
                        <h1 class="" style="text-align: center; font-size: 20px">Jovanne Diel Jabay</h1>
                        <p class="text-dark" style="text-align: center; font-size: 15px">Lead Developer</p>
                    </div>
                    <!-- Team Member 2 -->
                    <div class="col-12 col-sm-6 col-md-3 text-center team-member d-flex flex-column align-items-center" data-aos="fade-up" data-aos-delay="200">
                        <img src="{{ asset('assets/img/noel.jpeg') }}" alt="Team Member 2" class="mb-3">
                        <h1 class="" style="text-align: center; font-size: 20px">Noel Yanez</h1>
                        <p class="text-dark" style="text-align: center; font-size: 15px">Quality Assurance</p>
                    </div>
                    <!-- Team Member 3 -->
                    <div class="col-12 col-sm-6 col-md-3 text-center team-member d-flex flex-column align-items-center" data-aos="fade-up" data-aos-delay="300">
                        <img src="{{ asset('assets/img/gene.jpeg') }}" alt="Team Member 3" class="mb-3">
                        <h1 class="" style="text-align: center; font-size: 20px">Gene Gomera</h1>
                        <p class="text-dark" style="text-align: center; font-size: 15px">UI/UX Designer</p>
                    </div>
                    <!-- Team Member 4 -->
                    <div class="col-12 col-sm-6 col-md-3 text-center team-member d-flex flex-column align-items-center" data-aos="fade-up" data-aos-delay="400">
                        <img src="{{ asset('assets/img/aira.jpeg') }}" alt="Team Member 4" class="mb-3">
                        <h1 class="" style="text-align: center; font-size: 20px">Aira Maria Negre</h1>
                        <p class="text-dark" style="text-align: center; font-size: 15px">Documentations</p>
                    </div>
                    <!-- Add more team members as needed -->
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
        <footer class="bg-dark text-white py-4">
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


