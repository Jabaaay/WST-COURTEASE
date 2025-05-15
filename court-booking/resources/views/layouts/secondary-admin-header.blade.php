<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<link href="https://fonts.bunny.net/css?family=poppins:400,500,600" rel="stylesheet" />
<style>
    body
    {
        font-family: 'Poppins', sans-serif;
    }
</style>
<nav class="navbar p-0 fixed-top d-flex flex-row">

          <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
            
            <ul class="navbar-nav navbar-nav-right">

              <li class="nav-item dropdown">
                <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
                  <div class="navbar-profile">
                    <img class="img-xs rounded-circle" src="{{ asset('assets/images/faces/face15.jpg') }}" alt="">
                    
                    <p class="mb-0 d-none d-sm-block navbar-profile-name">{{ session('secondary_admin_name') }}</p>
                    <i class="mdi mdi-menu-down d-none d-sm-block"></i>
                  </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
                  <h6 class="p-3 mb-0">Profile</h6>
                  <div class="dropdown-divider"></div>
                  <a href="{{ route('secondary-admin.profile') }}" class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                      <div class="preview-icon bg-dark rounded-circle">
                        <i class="mdi mdi-account text-warning"></i>
                      </div>
                    </div>
                    <div class="preview-item-content">
                      <p class="preview-subject mb-1">Profile</p>
                    </div>
                  </a>
                  <a href="{{ route('secondary-admin.settings') }}" class="dropdown-item preview-item">
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
                  <form method="POST" action="{{ route('secondary-admin.secondaryAdminLogout') }}">
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

        <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
        <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
        <script src="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.js') }}"></script>

