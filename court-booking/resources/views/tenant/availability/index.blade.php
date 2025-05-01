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
                            <h4 class="card-title d-flex justify-content-between">Manage Availability
                                <a href="{{ route('tenant.availability.create') }}" class="btn btn-outline-primary btn-fw">+ Add Availability</a>
                            </h4>


                            <div class="table-responsive">

                            <table class="table">
                                <thead>
                                <tr>
             
                                    <th>Event Name</th>
                                    <th>Description</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($availabilities as $availability)
                                    <tr>
                                        <td>{{ $availability->event_name }}</td>
                                        <td>{{ $availability->description }}</td>
                                        <td>{{ $availability->start_date }}</td>
                                        <td>{{ $availability->end_date }}</td>
                                        <td>
                                            <a href="{{ route('tenant.availability.edit', $availability->id) }}" class="btn btn-outline-info">Edit</a>
                                            <form id="delete-form-{{ $availability->id }}" action="{{ route('tenant.availability.destroy', $availability->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete({{ $availability->id }})">Delete</button>
                                            </form>
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
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>


