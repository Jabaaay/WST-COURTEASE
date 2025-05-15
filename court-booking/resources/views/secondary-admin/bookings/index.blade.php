<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


<div class="container-scroller">

    @include('layouts.secondary-admin-sidebar')

    @include('layouts.secondary-admin-header')

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
                            <h4 class="card-title d-flex justify-content-between">Bookings
                            </h4>


                            <div class="table-responsive">

                            <table class="table">
                                <thead>
                                <tr class="text-center">
             
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Equipment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                            
                            
                                @foreach($bookings as $booking)
                                <tr class="text-center">
                                    <td class="text-center">{{ $booking->event_name }}</td>
                                    <td class="text-center">{{ $booking->start_date }}</td>
                                    <td class="text-center">{{ $booking->end_date }}</td>
                                    <td class="text-center">{{ $booking->equipment_request }}</td>
                                    <td class="text-center">
                                        @if($booking->status == 'pending')
                                            <span class="badge badge-outline-warning">Pending</span>
                                        @elseif($booking->status == 'confirmed')
                                            <span class="badge badge-outline-success">Approved</span>
                                        @elseif($booking->status == 'cancelled')
                                            <span class="badge badge-outline-danger">Rejected</span>
                                        @endif
                                    </td>
                                  
                                    <td class="text-center">

                                        @if($booking->status == 'pending')
                                        <form action="{{ route('secondary-admin.bookings.approve', $booking->id) }}" method="POST" style="display: inline;" class="approve-form">
                                            @csrf
                                            <button type="button" class="btn btn-outline-success approve-btn"><i class="mdi mdi-check"></i></button>
                                        </form>
                                        <form action="{{ route('secondary-admin.bookings.reject', $booking->id) }}" method="POST" style="display: inline;" class="reject-form">
                                            @csrf
                                            <button type="button" class="btn btn-outline-danger reject-btn"><i class="mdi mdi-close"></i></button>
                                        </form>
                                        
                                        @endif

                                        @if($booking->status == 'confirmed' || $booking->status == 'cancelled')
                                            <a href="{{ route('secondary-admin.bookings.show', $booking->id) }}" class="btn btn-outline-info btn-sm">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <form action="{{ route('secondary-admin.bookings.delete', $booking->id) }}" method="POST" style="display: inline;" id="delete-form-{{ $booking->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $booking->id }})"><i class="mdi mdi-delete"></i></button>
                                            </form>
                                        @endif
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

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this action!",
            imageUrl: 'https://cdn.jsdelivr.net/npm/bootstrap-icons/icons/exclamation-triangle-fill.svg',  // Custom Bootstrap icon
            imageWidth: 100,  // Resize the icon
            imageHeight: 100, // Resize the icon
            background: '#1E3E62',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-outline-danger', // Matches the Bootstrap style
                cancelButton: 'btn btn-outline-warning' // Matches the secondary cancel style
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    document.querySelectorAll('.approve-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approve Booking?',
                text: 'Are you sure you want to approve this booking?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
    document.querySelectorAll('.reject-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reject Booking?',
                text: 'Are you sure you want to reject this booking?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, reject',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
</script>





