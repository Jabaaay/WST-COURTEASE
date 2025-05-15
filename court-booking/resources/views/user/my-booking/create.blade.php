<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                            <h4 class="card-title d-flex justify-content-between">Create Booking
                            </h4>

                            @if(session('error'))
                                <script>
                                    Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            imageUrl: 'https://cdn.jsdelivr.net/npm/bootstrap-icons/icons/exclamation-triangle-fill.svg',  // Custom Bootstrap icon
            imageWidth: 100,  // Resize the icon
            imageHeight: 100, // Resize the icon
            background: '#1E3E62',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok',
            customClass: {
                confirmButton: 'btn btn-outline-danger btn-fw', // Matches the Bootstrap style
            }
        })
                                </script>
                            @endif

                            <form action="{{ route('user.my-booking.store') }}" method="post" id="bookingForm">
                                @csrf
                                <div class="form-group">
                                    <label for="event_name">Event Name</label>
                                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                                </div>
                              
                                <div class="form-group">
                                    <label>Equipment Request</label>
                                    <div id="selectedEquipment" class="mb-2"></div>
                                    <div class="form-check form-check-warning">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="chair" onchange="updateSelectedEquipment()"> Chair
                                        </label>
                                    </div>

                                    <div class="form-check form-check-warning">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="table" onchange="updateSelectedEquipment()"> Table
                                        </label>
                                    </div>

                                    <div class="form-check form-check-warning">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="projector" onchange="updateSelectedEquipment()"> Projector
                                        </label>
                                    </div>

                                    <div class="form-check form-check-warning">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="speaker" onchange="updateSelectedEquipment()"> Speaker
                                        </label>
                                    </div>

                                    <div class="form-check form-check-warning">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="other" onchange="toggleInput()"> Other
                                        </label>
                                    </div>

                                    <div id="other_request" style="display: none;" class="mt-2 form-group">
                                        <input type="text" class="form-control" id="other_request_input" name="other_request" placeholder="Please Specify">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="number_of_participants">Number of Participants</label>
                                    <input type="number" class="form-control" id="number_of_participants" name="number_of_participants" required>
                                </div>
                                

                                <div class="form-group d-flex justify-content-between">
                                    <button type="submit" class="btn btn-outline-info btn-fw">Create Event</button>
                                    <a href="{{ route('user.my-booking.index') }}" class="btn btn-outline-danger btn-fw">Cancel</a>
                                </div>

                            </form>
                                
                                

                    
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
function toggleInput() {
    var checkbox = document.querySelector('input[value="other"]');
    var inputField = document.getElementById('other_request');
    
    if (checkbox.checked) {
        inputField.style.display = 'block';
    } else {
        inputField.style.display = 'none';
        document.getElementById('other_request_input').value = '';
    }
    updateSelectedEquipment();
}
// Add event listener for other request input
document.getElementById('other_request_input').addEventListener('input', updateSelectedEquipment);

// Date validation
document.getElementById('start_date').addEventListener('change', validateDates);
document.getElementById('end_date').addEventListener('change', validateDates);

function validateDates() {
    const startDate = new Date(document.getElementById('start_date').value);
    const endDate = new Date(document.getElementById('end_date').value);
    const now = new Date();

    if (startDate < now) {
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
        });
        document.getElementById('start_date').value = '';
        return false;
    }

    if (endDate < startDate) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Date!',
            text: 'End date must be after start date.',
            confirmButtonColor: '#3085d6'
        });
        document.getElementById('end_date').value = '';
        return false;
    }

    return true;
}

// Form submission validation
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    if (!validateDates()) {
        e.preventDefault();
        return false;
    }
});
</script>



