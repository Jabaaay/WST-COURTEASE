<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Corona Admin</title>
    <!-- plugins:css -->

    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel-2/owl.theme.default.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600" rel="stylesheet" />
  </head>
  <style>
    body
    {
      font-family: 'Poppins', sans-serif;
    }
  </style>
  <body>

      <!-- partial -->
      
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar p-0 fixed-top d-flex flex-row">

          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">


            <ul class="navbar-nav navbar-nav-right">

              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="{{ asset('assets/images/faces/face15.jpg') }}" alt="">
                    <p class="mb-0 d-none d-sm-block navbar-profile-name">{{ Auth::user()->name }}</p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0">Profile</h6>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-settings text-success"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Settings</p>
                    </div>
                  </a>
                  <div class="dropdown-divider"></div>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-logout text-danger"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">
                        
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-danger text-decoration-none">
                                {{ __('Log Out') }}
                            </button>
                        
                      </p>
                    </div>
                    
                  </a>
                  </form>
              </li>
            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-format-line-spacing"></span>
            </button>
          </div>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="row ">
              <div class="col-12 grid-margin">
                
              @if (session('success'))
                <div class="alert alert-success">
                  {{ session('success') }}
                </div>
              @endif

              @if (session('error'))
                <div class="alert alert-danger">
                  {{ session('error') }}
                </div>
              @endif

                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Tenant List</h4>
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Tenant Name</th>
                            <th>Email</th>
                            <th>Domain</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->email }}</td>
                                    <td>{{ $tenant->domain }}.localhost</td>
                                    <td>
                                       <!-- status if pending and approved -->
                                       @if ($tenant->status == 'pending')
                                        <div class="badge badge-outline-warning">Pending</div>
                                       @elseif ($tenant->status == 'accepted')
                                        <div class="badge badge-outline-success">Enabled</div>
                                       @elseif ($tenant->status == 'disabled')
                                        <div class="badge badge-outline-danger">Disabled</div>
                                       @elseif ($tenant->status == 'enabled')
                                        <div class="badge badge-outline-success">Enabled</div>
                                       @endif
                                    </td>

                                    <td>
                                        @if ($tenant->status == 'pending')
                                            <form method="POST" action="{{ route('tenant.accept', $tenant->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Accept</button>
                                            </form>
                                        @elseif ($tenant->status == 'accepted')
                                            <form method="POST" action="{{ route('tenant.disable', $tenant->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning">Disable</button>
                                            </form>

                                            @if ($tenant->plan == 'basic')
                                            <form method="POST" action="{{ route('tenant.premium', $tenant->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-info">Premium</button>
                                            </form>
                                            @endif

                                            @if ($tenant->plan == 'premium')
                                                <form method="POST" action="{{ route('tenant.basic', $tenant->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger">Basic</button>
                                                </form>
                                            @endif
                                            
                                            @elseif ($tenant->status == 'disabled')
                                                <form method="POST" action="{{ route('tenant.enable', $tenant->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success">Enable</button>
                                                </form>

                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
          <!-- partial -->
        </div>
        
        <!-- main-panel ends -->
      
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}  "></script>
    <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script src="{{ asset('assets/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <!-- End custom js for this page -->
  </body>
</html>