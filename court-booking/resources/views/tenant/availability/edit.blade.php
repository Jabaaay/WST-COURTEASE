<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">



<div class="container-scroller">

    @include('layouts.tenant-sidebar')

    @include('layouts.tenant-header')

        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">

                <div class="content-wrapper">
                    <div class="row ">
                    <div class="col-12 grid-margin">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title d-flex justify-content-between">Edit Availability
                            </h4>

                            <form action="{{ route('tenant.availability.update', $availability->id) }}" method="POST" id="updateForm">
                                @csrf
                                @method('PUT')

                            <div class="form-group mb-3">
                                <label for="event_name">Event Name</label>
                                <input type="text" name="event_name" id="event_name" class="form-control" placeholder="Event Name" value="{{ $availability->event_name }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description">{{ $availability->description }}</textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="start_date">Start Date</label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control" placeholder="Start Date" value="{{ $availability->start_date }}">
                            </div>

                            <div class="form-group mb-3">
                                <label for="end_date">End Date</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control" placeholder="End Date" value="{{ $availability->end_date }}">
                            </div>


                            <div class="d-flex justify-content-between">

                            <button type="submit" class="btn btn-outline-info btn-fw" >Update</button>

                            </form>
                            
                            <a href="{{ route('tenant.availability.index') }}" class="btn btn-outline-danger btn-fw">Cancel</a>
                            </div>

                          

                            
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.js"></script>
<script>
   
   document.getElementById('updateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this action!",
        imageUrl: 'https://cdn.jsdelivr.net/npm/bootstrap-icons/icons/question-circle-fill.svg',
        imageWidth: 100,
        imageHeight: 100,
        background: '#1E3E62',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-outline-warning', // Matches the Bootstrap style
            cancelButton: 'btn btn-outline-danger' // Matches the secondary cancel style
        }
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
   });

</script>

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



