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

<nav class="sidebar sidebar-offcanvas" id="sidebar">
        <div class="sidebar-brand-wrapper  d-flex align-items-center justify-content-center fixed-top">
            <a href="{{ route('tenant.dashboard') }}"><img src="{{ asset('assets/img/COURT.png') }}" alt="" style="width: 150px; height: 100px;"></a>
        </div>
        <ul class="nav">
          <li class="nav-item menu-items">
            <a class="nav-link {{ Request::is('tenant/dashboard') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('tenant.dashboard') }}">
              <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
              </span>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          <li class="nav-item menu-items">
            <a class="nav-link {{ Request::is('tenant/secondary-admins*') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('tenant.secondary-admins') }}">
                <span class="menu-icon">
                <i class="mdi mdi-account-plus"></i>
                </span>
                <span class="menu-title">Secondary Admins</span>
            </a>
            </li>

          <li class="nav-item menu-items">
            <a class="nav-link {{ Request::is('tenant/bookings*') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('tenant.bookings.index') }}">
              <span class="menu-icon">
                <i class="mdi mdi-calendar-check"></i>
              </span>
              <span class="menu-title">Bookings</span>
            </a>
          </li>
          <li class="nav-item menu-items">
            <a class="nav-link {{ Request::is('tenant/calendar*') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('tenant.calendar') }}">
              <span class="menu-icon">
                <i class="mdi mdi-calendar-month"></i>
              </span>
              <span class="menu-title">Calendar</span>
            </a>
          </li>
          <li class="nav-item menu-items">
            <a class="nav-link {{ Request::is('tenant/availability*') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('tenant.availability.index') }}">
              <span class="menu-icon">
                <i class="mdi mdi-calendar-check"></i>
              </span>
              <span class="menu-title">Manage Availability</span>
            </a>
          </li>
          <li class="nav-item menu-items">
            <a class="nav-link {{ Request::is('tenant/users*') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('tenant.users.index') }}">
              <span class="menu-icon">
                <i class="mdi mdi-account-group"></i>
              </span>
              <span class="menu-title">Users</span>
            </a>
          </li>
          
        </ul>
      </nav>

                <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
                <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
                <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
                <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
                <script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
                <script src="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.js') }}"></script>

<script src="{{ asset('assets/js/theme-settings.js') }}"></script>

<script>
    // Apply theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('themeSettings');
        if (savedTheme) {
            const theme = JSON.parse(savedTheme);
            // Apply sidebar colors
            const sidebar = document.querySelector('.sidebar');
            sidebar.style.backgroundColor = theme.sidebar;
            sidebar.style.color = theme.sidebarText;
            
            // Apply text color to all text elements in sidebar
            const textElements = sidebar.querySelectorAll('.nav-item, .nav-link, .menu-title');
            textElements.forEach(element => {
                element.style.color = theme.sidebarText;
            });
        }
    });
</script>

