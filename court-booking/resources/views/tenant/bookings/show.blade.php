<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-scroller">

    @include('layouts.tenant-sidebar')

    @include('layouts.tenant-header')

        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">

                <div class="content-wrapper">
                    <div class="row ">

                    <div class="col-12 grid-margin">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                        <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                            <h4 class="card-title">Booking Details</h4>
                            <div class="dropdown">
                                <a href="{{ route('tenant.bookings.index') }}" class="btn btn-outline-primary btn-fw d-flex align-items-center justify-content-center"><i class="mdi mdi-arrow-left"></i> Back</a>
                            </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th class="" style="width: 50px;">Booking ID</th>
                                            <td style="width: 50px;">{{ $booking->id }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Name</th>
                                            <td>{{ $booking->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Event Name</th>
                                            <td>{{ $booking->event_name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Description</th>
                                            <td>{{ $booking->description }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Equipment</th>
                                            <td>{{ $booking->equipment_request }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Number of Participants</th>
                                            <td>{{ $booking->number_of_participants }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Start Date</th>
                                            <td>{{ $booking->start_date }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">End Date</th>
                                            <td>{{ $booking->end_date }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">Status</th>
                                            <td>
                                                @if($booking->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge badge-success">Confirmed</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="">Booked At</th>
                                            <td>{{ $booking->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-left">
                                                <a href="{{ route('tenant.bookings.destroy', $booking->id) }}" class="btn btn-outline-danger btn-fw" onclick="return confirm('Are you sure you want to delete this booking?')">Delete</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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




