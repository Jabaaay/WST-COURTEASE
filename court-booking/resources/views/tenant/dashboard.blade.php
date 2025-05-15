<link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


<div class="container-scroller">

    @include('layouts.tenant-sidebar')

    @include('layouts.tenant-header')

        <div class="container-fluid page-body-wrapper">
            <div class="main-panel">

                <div class="content-wrapper">

                <div class="row" id="draggable-cards">
                <div class="col-sm-3 grid-margin draggable-card">
                <div class="card border-info">
                  <div class="card-body">
                    <h5>Total Bookings</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0">{{ $allBookings }}</h2>
                        </div>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-calendar-check-outline text-info ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-3 grid-margin draggable-card">
                <div class="card border-success">
                  <div class="card-body">
                    <h5>Approved</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0">{{ $approvedBookings }}</h2>
                        </div>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-check-circle-outline text-success ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-3 grid-margin draggable-card">
                <div class="card border-danger">
                  <div class="card-body">
                    <h5>Rejected</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h2 class="mb-0">{{ $rejectedBookings }}</h2>
                        </div>
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-close-circle-outline text-danger ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-3 grid-margin draggable-card">
                <div class="card border-primary">
                  <div class="card-body">
                    <h5>Users</h5>
                    <div class="row">
                      <div class="col-8 col-sm-12 col-xl-8 my-auto">
                        <div class="d-flex d-sm-block d-md-flex align-items-center">
                          <h1 class="mb-0">{{ $userCount }}</h1>
                    
                        </div>
              
                      </div>
                      <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                        <i class="icon-lg mdi mdi-account-group-outline text-primary ml-auto"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              

                </div>
                
                    <div class="row " id="draggable-charts">


                  

                    <div class="col-lg-6 grid-margin stretch-card draggable-card">
                      <div class="card">
                        <div class="card-body">
                          <h4 class="card-title">Bar chart</h4>
                          <canvas id="barChart" style="height:230px"></canvas>
                        </div>
                      </div>
                    </div>

              <div class="col-lg-6 grid-margin stretch-card draggable-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Pie chart</h4>
                    <canvas id="pieChart" style="height:250px"></canvas>
                  </div>
                </div>
              </div>
                    <div class="col-12 grid-margin draggable-card">
                        <div class="card">
                        <div class="card-body">
                            <h4 class="card-title d-flex justify-content-between">Recent Bookings
                            </h4>


                            <div class="table-responsive">

                            <table class="table">
                                <thead>
                                <tr>
             
                                <th>Name</th>
                                    <th>Event Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Equipment</th>
                                    <th>Participants</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                      
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->name }}</td>
                                    <td>{{ $booking->event_name }}</td>
                                    <td>{{ $booking->start_date }}</td>
                                    <td>{{ $booking->end_date }}</td>
                                    <td>{{ $booking->equipment_request }}</td>
                                    <td>{{ $booking->number_of_participants }}</td>
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

                                <tr>
                                  @if($bookings->count() == 0)
                                  <td colspan="7" class="text-center">
                                    No bookings found
                                  </td>
                                  @endif
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

<script src="{{ asset('assets/js/chart.js') }}"></script>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
  var bookingStats = {
    approved: {{ $approvedBookings ?? 0 }},
    rejected: {{ $rejectedBookings ?? 0 }},
    pending: {{ $pendingBookings ?? 0 }}
  };

  $(document).ready(function() {
    // Make cards draggable
    $(".draggable-card").draggable({
      handle: ".card",
      cursor: "move",
      revert: "invalid",
      helper: "clone",
      start: function(event, ui) {
        $(this).css("opacity", "0.5");
      },
      stop: function(event, ui) {
        $(this).css("opacity", "1");
      }
    });

    // Make the container droppable
    $("#draggable-cards, #draggable-charts").droppable({
      accept: ".draggable-card",
      drop: function(event, ui) {
        var droppedCard = ui.draggable;
        var targetContainer = $(this);
        
        // Move the card to the new position
        droppedCard.appendTo(targetContainer);
        
        // Reinitialize the grid system
        droppedCard.find('.col-sm-3, .col-lg-6').removeClass('col-sm-3 col-lg-6').addClass('col-sm-3');
      }
    });
  });
</script>









