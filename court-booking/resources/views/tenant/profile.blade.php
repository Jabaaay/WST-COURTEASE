<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />


<div class="container-scroller">

    @include('layouts.tenant-sidebar')

    @include('layouts.tenant-header')

        <div class="container-fluid page-body-wrapper">
        <div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card corona-gradient-card">
                    <div class="card-body py-0 px-0 px-sm-3">
                        <div class="row align-items-center">
                            <div class="col-4 col-sm-3 col-xl-2">
                                <img src="{{ asset('assets/images/dashboard/Group126@2x.png') }}" class="gradient-corona-img img-fluid" alt="">
                            </div>
                            <div class="col-5 col-sm-7 col-xl-8 p-0">
                                <h4 class="mb-1 mb-sm-0">Welcome to your profile!</h4>
                                <p class="mb-0 font-weight-normal d-none d-sm-block">Update your personal information here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Profile Information</h4>
                    
                            
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control text-muted" id="first_name" name="first_name" value="{{ session('tenant_name') }}" readonly>
                            </div>



                            <div class="form-group">
                                <label for="email">Email address</label>
                                    <input type="email" class="form-control text-muted" id="email" name="email" value="{{ session('tenant_email') }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="email">Role</label>
                                <input type="text" class="form-control text-muted" id="email" name="email" value="Tenant Admin" readonly>
                            </div>


                         
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>

</div>

<script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/vendors/owl-carousel-2/owl.carousel.min.js') }}"></script>

<script src="{{ asset('assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('assets/js/misc.js') }}"></script>
<script src="{{ asset('assets/js/settings.js') }}"></script>
<script src="{{ asset('assets/js/todolist.js') }}"></script>

<script src="{{ asset('assets/js/dashboard.js') }}"></script>

<script src="{{ asset('assets/js/chart.js') }}"></script>










