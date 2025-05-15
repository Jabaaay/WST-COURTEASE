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
                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit Booking</h4>
                                <form action="{{ route('user.my-booking.update', $booking->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="event_name">Event Name</label>
                                        <input type="text" class="form-control" id="event_name" name="event_name" value="{{ $booking->event_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5">{{ $booking->description }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $booking->start_date }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $booking->end_date }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="number_of_participants">Number of Participants</label>
                                        <input type="number" class="form-control" id="number_of_participants" name="number_of_participants" value="{{ $booking->number_of_participants }}">
                                    </div>
                                 


                                    <div class="form-group">
                                        <label>Equipment Request</label>
                                        <div class="form-check form-check-warning">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="chair" onchange="updateSelectedEquipment()" {{ str_contains($booking->equipment_request, 'chair') ? 'checked' : '' }}> Chair
                                            </label>
                                        </div>

                                        <div class="form-check form-check-warning">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="table" onchange="updateSelectedEquipment()" {{ str_contains($booking->equipment_request, 'table') ? 'checked' : '' }}> Table
                                            </label>
                                        </div>

                                        <div class="form-check form-check-warning">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="projector" onchange="updateSelectedEquipment()" {{ str_contains($booking->equipment_request, 'projector') ? 'checked' : '' }}> Projector
                                            </label>
                                        </div>

                                        <div class="form-check form-check-warning">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="speaker" onchange="updateSelectedEquipment()" {{ str_contains($booking->equipment_request, 'speaker') ? 'checked' : '' }}> Speaker
                                            </label>
                                        </div>

                                        <div class="form-check form-check-warning">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input equipment-checkbox" name="equipment_request[]" value="other" onchange="toggleInput()" {{ !in_array($booking->equipment_request, ['chair', 'table', 'projector', 'speaker']) ? 'checked' : '' }}> Other
                                            </label>
                                        </div>

                                        <div id="other_request" style="display: {{ !in_array($booking->equipment_request, ['chair', 'table', 'projector', 'speaker']) ? 'block' : 'none' }};" class="mt-2 form-group">
                                            <input type="text" class="form-control" id="other_request_input" name="other_request" placeholder="Please Specify" value="{{ !in_array($booking->equipment_request, ['chair', 'table', 'projector', 'speaker']) ? $booking->equipment_request : '' }}">
                                        </div>
                                    </div>

                                    <div class="form-group d-flex justify-content-between">
                                        <button type="submit" class="btn btn-outline-info btn-fw">Update</button>
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

<script src="{{ asset('assets/js/chart.js') }}"></script>

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

function updateSelectedEquipment() {
    var checkboxes = document.querySelectorAll('.equipment-checkbox:checked');
    var selectedDiv = document.getElementById('selectedEquipment');
    var selectedItems = Array.from(checkboxes).map(cb => {
        if (cb.value === 'other') {
            var otherValue = document.getElementById('other_request_input').value;
            return otherValue ? otherValue : 'Other';
        }
        return cb.value;
    });

    if (selectedItems.length > 0) {
        selectedDiv.innerHTML = '<strong>Selected Equipment:</strong> ' + selectedItems.join(', ');
    } else {
        selectedDiv.innerHTML = '';
    }
}

// Add event listener for other request input
document.getElementById('other_request_input').addEventListener('input', updateSelectedEquipment);

// Initialize selected equipment display
updateSelectedEquipment();
</script>