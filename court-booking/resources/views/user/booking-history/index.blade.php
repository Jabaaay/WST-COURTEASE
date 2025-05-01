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
       

              
                    <div class="col-12 grid-margin">
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title d-flex justify-content-between">History Bookings
                            </h4>


                            <div class="table-responsive">

                            <table class="table">
                                <thead>
                                <tr>
             
                                    
                                <th class="text-center">Event Name</th>
                                <th class="text-center">Booking Date</th>
                                <th class="text-center">Equipment Request</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>

                                </tr>
                                </thead>
                                <tbody>
                                    
                                @foreach($bookings as $booking)
                                <tr>
                                    <td class="text-center">{{ $booking->event_name }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($booking->created_at)->timezone('Asia/Manila')->format('F j, Y g:i A') }}</td>
                                    <td class="text-center">{{ $booking->equipment_request }}</td>
                                    <td class="text-center">
                                        @if($booking->status == 'pending')
                                            <span class="badge badge-outline-warning">Pending</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span class="badge badge-outline-success">Confirmed</span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="badge badge-outline-danger">Rejected</span>
                                        @endif
                                    

                                    </td>

                                    <td class="text-center">
                                        
                                            <form action="" method="POST" style="display: inline;">
                                              
                                            <button type="submit" class="btn btn-outline-info btn-sm"><i class="mdi mdi-eye"></i></button>
                                            </form>

                                            <form action="" method="POST" style="display: inline;">
                                              
                                              <button type="submit" class="btn btn-outline-warning btn-sm"><i class="mdi mdi-archive"></i></button>
                                              </form>

                                              <form action="" method="POST" style="display: inline;">
                                              
                                              <button type="submit" class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete"></i></button>
                                              </form>
                                       
                                    </td>

                                </tr>
                                
                                @endforeach
                                    
                                </tbody>
                            </table>
                            <div class="mt-3 d-flex justify-content-between align-items-center ">
                                {{ $bookings->links('pagination::bootstrap-5') }}
                            </div>
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










