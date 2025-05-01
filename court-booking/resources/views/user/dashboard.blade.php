<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />


<div class="container-scroller">

    @include('layouts.user-sidebar')

    @include('layouts.user-header')

        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">

        

                <div class="content-wrapper">

                


                
                    <div class="row ">
       
                    <div class="col-sm-3 grid-margin">
                <div class="card border-primary">
                  <div class="card-body">
                    <h5>Total Bookings</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                           <h1 class="mb-0">{{ $allBookings }}</h1>
                        </div>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-calendar-check-outline text-warning ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
                    <div class="col-12 grid-margin">
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title d-flex justify-content-between">Recent Bookings
                            </h4>


                            <div class="table-responsive">

                            <table class="table">
                                <thead>
                                <tr>
             
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Participants</th>
                                    <th>Equipment</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->event_name }}</td>
                                    <td>{{ $booking->start_date }} - {{ $booking->end_date }}</td>
                                    <td>{{ $booking->number_of_participants }}</td>
                                    <td>{{ $booking->equipment_request }}</td>
                                    <td>
                                        @if($booking->status == 'pending')
                                            <span class="badge badge-outline-warning">Pending</span>
                                        @elseif($booking->status == 'approved')
                                            <span class="badge badge-outline-success">Approved</span>
                                        @elseif($booking->status == 'rejected')
                                            <span class="badge badge-outline-danger">Rejected</span>
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










