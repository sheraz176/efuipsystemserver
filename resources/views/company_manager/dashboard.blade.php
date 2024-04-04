@extends('company_manager.layout.master')

@section('content')
@if(session('status'))
<div class="alert alert-success">
    {{ session('status') }}
</div>
@endif

<h4 class=""><span class="text-muted fw-light">Company Performance/</span> Daily Lifes Secured & Total Sales</h4>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="d-block mb-1">Current Year Lifes Secured</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($currentYearSubscriptionCount, 0, '.', ',') }}</h3>
                            <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Current Month Total Lifes Secured</span>
                            <h3 class="card-title mb-2">{{ number_format($currentMonthSubscriptionCount, 0, '.', ',') }}</h3>
                            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                        </div>
                    </div>
                </div>



            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/chart.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="d-block mb-1">Today's Total Lifes Secured</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($todaySubscriptionCount, 0, '.', ',') }}</h3>
                            <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Current Year Total Sales(Company)</span>
                            <h3 class="card-title mb-2">{{ number_format($yearlyTransactionSum, 0, '.', ',') }}</h3>
                            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                        </div>
                    </div>
                </div>



            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/chart.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="d-block mb-1">Current Month's Total Sales(Company)</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($monthlyTransactionSum, 0, '.', ',') }}</h3>
                            <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-medium d-block mb-1">Today's Total Sales (Company)</span>
                            <h3 class="card-title mb-2">{{ number_format($dailyTransactionSum, 0, '.', ',') }}</h3>
                            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>


</div>
<h4 class=""><span class="text-muted fw-light">Company (Manager) Performance/</span> (Graphs)</h4>

<div class=row>

    <div class="col-xl-6 col-12 mb-4">
        <div class="card">
            <div class="card-header header-elements">
                <h5 class="card-title mb-0">Net Enrollments </h5>
                <div class="card-action-element ms-auto py-0">
                    <div class="dropdown">
                        <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="today">Today</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="yesterday">Yesterday</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="last_7_days">Last 7 Days</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="last_30_days">Last 30 Days</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="current_month">Current Month</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="last_month">Last Month</a></li>
                    <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="this_year">This Year</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="barChart" class="chartjs" data-height="400" height="500" style="display: block; box-sizing: border-box; height: 400px; width: 519px;" width="649"></canvas>
            </div>
        </div>
    </div>
    <!-- /Bar Charts -->

    <div class="col-xl-6 col-12 mb-4">
        <div class="card">
            <div class="card-header header-elements">
                <h5 class="card-title mb-0">Active Customers</h5>
                <div class="card-action-element ms-auto py-0">
                    {{-- <div class="dropdown">
                        <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="today">Today</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="yesterday">Yesterday</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="last_7_days">Last 7 Days</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="last_30_days">Last 30 Days</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="current_month">Current Month</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center" data-range="last_month">Last Month</a></li>
                        </ul>
                    </div> --}}
                </div>
            </div>
            <div class="card-body">
                <canvas id="barChart_1" class="chartjs" data-height="400" height="500" style="display: block; box-sizing: border-box; height: 400px; width: 519px;" width="649"></canvas>
            </div>
        </div>
    </div>

    <div class=row>
        <!-- Line Charts -->
        <div class="col-xl-6 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <div>
                        <h5 class="card-title mb-0">Last 30(Days) Total Enrollments</h5>

                    </div>

                </div>
                <div class="card-body">
                    <canvas id="EnrollmentlineChart" class="chartjs" data-height="500" height="625" width="1391" style="display: block; box-sizing: border-box; height: 500px; width: 1112px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <div>
                        <h5 class="card-title mb-0">Last 30(Days) Total  Refunded</h5>

                    </div>
                </div>
                <div class="card-body">
                    <canvas id="lineChartRefunded" class="chartjs" data-height="500" height="625" width="1391" style="display: block; box-sizing: border-box; height: 500px; width: 1112px;"></canvas>
                </div>

            </div>
        </div>


    </div>
</div>












<script>

    $(document).ready(function () {
        // Initial fetch for current month data
        fetchChartData('current_month');

        // Dropdown click event handler
        $('.dropdown-menu .dropdown-item').click(function() {
            var timeRange = $(this).data('range');
            fetchChartData(timeRange);
        });
    });

    function fetchChartData(timeRange) {
        // AJAX request to fetch data based on the selected time range
        $.ajax({
            url: '{{ route('companymanager.get-subscription-chart-data') }}',
            type: 'GET',
            data: { time_range: timeRange }, // Pass the time range parameter
            dataType: 'json',
            success: function (data) {
                // Update the chart with the fetched data
                updateChart(data);
            },
            error: function (error) {
                console.error('Error fetching data:', error);
            }
        });
    }


    var barChart = null; // Declare barChart variable outside the updateChart function

    function updateChart(data) {
        // Check if a previous Chart instance exists and destroy it
        if (barChart) {
            barChart.destroy();
        }

        // Extract necessary data from the fetched response
        var labels = data.labels;  // Array of date labels
        var values = data.values;  // Array of corresponding subscription counts

        // Get the chart canvas
        var ctx = document.getElementById('barChart').getContext('2d');

        // Create a new bar chart
        barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Subscription Counts',
                    data: values,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',  // Example color
                    borderColor: 'rgba(75, 192, 192, 1)',       // Example color
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }





            $(document).ready(function() {
                // Fetch data from the server
                $.ajax({
                    url: '{{ route('companymanager.getMonthlyActiveSubscriptionChartData') }}'
                    , type: 'GET'
                    , dataType: 'json'
                    , success: function(data) {
                        // Update the chart with the fetched data
                        updateChart_2(data, 'barChart_1'); // Pass chart ID as an argument
                    }
                    , error: function(error) {
                        console.error('Error fetching data:', error);
                    }
                });
            });

            function updateChart_2(data, chartId) {
                // Extract necessary data from the fetched response
                var labels = data.labels; // Array of month names
                var values = data.values; // Array of corresponding active subscription counts

                // Get the chart canvas
                var ctx = document.getElementById(chartId).getContext('2d');

                // Create a new bar chart
                var barChart = new Chart(ctx, {
                    type: 'bar'
                    , data: {
                        labels: labels
                        , datasets: [{
                            label: 'Monthly Active Subscriptions'
                            , data: values
                            , backgroundColor: 'rgba(75, 192, 192, 0.2)', // Example color
                            borderColor: 'rgba(75, 192, 192, 1)', // Example color
                            borderWidth: 1
                        }]
                    }
                    , options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

</script>
<script>
    // Enrollment Line Chart Start
    var CustomerSubscriptionData = <?php echo json_encode($CustomerSubscriptionData); ?>;

    // Get labels and data for the chart
    var Enrollmentlabels = Object.keys(CustomerSubscriptionData);
    var Enrollmentdata = Object.values(CustomerSubscriptionData);

    // Render the chart
    var ctx = document.getElementById('EnrollmentlineChart').getContext('2d');
      // Create a new line chart
      var EnrollmentlineChart = new Chart(ctx, {
                type: 'line'
                , data: {
                    labels: Enrollmentlabels
                    , datasets: [{
                        label: 'Enrollments'
                        , data: Enrollmentdata
                        , borderColor: 'rgba(75, 192, 192, 1)', // Example color for subscriptions
                        borderWidth: 2
                        , fill: false
                    }]
                }
                , options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

  // Get Refunded Line Chart

    var RefundedCustomersData = <?php echo json_encode($RefundedCustomersData); ?>;
    // Get labels and data for the chart

    var Refundedlabels = Object.keys(RefundedCustomersData);
    var Refundeddata = Object.values(RefundedCustomersData);

    // Render the chart
    var ctx = document.getElementById('lineChartRefunded').getContext('2d');
      // Create a new line chart
      var lineChartRefunded = new Chart(ctx, {
                type: 'line'
                , data: {
                    labels: Refundedlabels
                    , datasets: [ {
                        label: 'Refunded'
                        , data: Refundeddata
                        , borderColor: 'rgba(255, 99, 132, 1)', // Example color for unsubscriptions
                        borderWidth: 2
                        , fill: false
                    }]
                }
                , options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });


</script>

@endsection()
