<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">


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
                            <h4 class="card-title d-flex justify-content-between">Check Availability
                            </h4>


                            <div id='calendar'></div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white d-flex justify-content-between">
                <h3 class="modal-title" id="bookingModalLabel">Booking Details</h3>

            </div>
            <div class="modal-body">
                <div class="booking-details">
                    <h4 class="name"></h4>
                    <p class="event-title"></p>
                    <p class="start-date"></p>
                    <p class="end-date"></p>
                    <p class="description"></p>
                    <p class="participant"></p>
                    <p class="request"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary btn-fw" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

                            <div class="col-md-6">
                          <div class="form-group">

                          <div class="form-check form-check-secondary">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="ExampleRadio4" id="ExampleRadio4" disabled> Available </label>
                            </div>

                            <div class="form-check form-check-success">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="ExampleRadio2" id="ExampleRadio2" checked> Occupied </label>
                            </div>
                            <div class="form-check form-check-primary">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="ExampleRadio3" id="ExampleRadio3" checked> Official Use </label>
                            </div>

                            <div class="form-check form-check-warning">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="ExampleRadio4" id="ExampleRadio4" checked> Today </label>
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

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: [
                @foreach($availabilities as $availability)
                    {
                        
                        title: '{{ $availability->event_name }}',
                        start: '{{ $availability->start_date }}',
                        end: '{{ $availability->end_date }}',
                        backgroundColor: '#3788d8', // Blue for available
                        borderColor: '#3788d8'
                    }{{ !$loop->last ? ',' : '' }}
                @endforeach
                @if($availabilities->count() > 0 && $bookings->count() > 0),@endif
                @foreach($bookings as $booking)
                    {
                        title: '{{ $booking->event_name }}',
                        start: '{{ $booking->start_date }}',
                        end: '{{ $booking->end_date }}',
                        backgroundColor: '#28a745', // Green for booked
                        borderColor: '#28a745',
                        extendedProps: {
                            name: '{{ $booking->name }}',
                            event_name: '{{ $booking->event_name }}',
                            description: '{{ $booking->description }}',
                            start_time: '{{ $booking->start_date }}',
                            end_time: '{{ $booking->end_date }}',
                            participant: '{{ $booking->number_of_participants }}',
                            request: '{{ $booking->equipment_request }}'
                        }
                    }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ],
            eventClick: function(info) {
                if (info.event.backgroundColor === '#28a745') { // Only show modal for bookings
                    $('#bookingModal .name').text('Name: ' + info.event.    extendedProps.name);
                    $('#bookingModal .event-title').text('Event Name: ' + info.event.extendedProps.event_name);
                    $('#bookingModal .start-date').text('Start Date: ' + info.event.extendedProps.start_time);
                    $('#bookingModal .end-date').text('End Date: ' + info.event.extendedProps.end_time);
                    $('#bookingModal .description').text('Description: ' + info.event.extendedProps.description);
                    $('#bookingModal .participant').text('Number of Participants: ' + info.event.extendedProps.participant);
                    $('#bookingModal .request').text('Equipment Request: ' + info.event.extendedProps.request);
                    $('#bookingModal').modal('show');
                }
            },
            eventDidMount: function(info) {
                // Add tooltip
                $(info.el).tooltip({
                    title: info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
        calendar.render();
    });
</script>










