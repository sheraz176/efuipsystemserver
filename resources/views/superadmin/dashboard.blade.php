@extends('superadmin.layout.master')

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
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div> --}}
                            </div>
                            <span class="d-block mb-1">Current Year Lifes Secured</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($currentYearSubscriptionCount, 0, '.', ',') }}</h3>
                            {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
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
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div> --}}
                            </div>
                            <span class="fw-medium d-block mb-1">Current Month Total Lifes Secured</span>
                            <h3 class="card-title mb-2">{{ number_format($currentMonthSubscriptionCount, 0, '.', ',') }}</h3>
                            {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
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
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div> --}}
                            </div>
                            <span class="d-block mb-1">Today's Total Lifes Secured</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($todaySubscriptionCount, 0, '.', ',') }}</h3>
                            {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
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
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div> --}}
                            </div>
                            <span class="fw-medium d-block mb-1">Current Year Total Sales(Company)</span>
                            <h3 class="card-title mb-2">{{ number_format($yearlyTransactionSum, 0, '.', ',') }}</h3>
                            {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
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
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div> --}}
                            </div>
                            <span class="d-block mb-1">Current Months Total Sales</span>
                            <h3 class="card-title text-nowrap mb-2">{{ number_format($monthlyTransactionSum, 0, '.', ',') }}</h3>
                            {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
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
                                {{-- <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="cardOpt1">
                                        <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                                    </div>
                                </div> --}}
                            </div>
                            <span class="fw-medium d-block mb-1">Today's Total Sales (Company)</span>
                            <h3 class="card-title mb-2">{{ number_format($dailyTransactionSum, 0, '.', ',') }}</h3>
                            {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



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

                            </div>
                            <span class="d-block mb-1">Total Tsm Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($totalTsm, 0, '.', ',') }}</h3>
                            <span class="d-block mb-1">Active Tsm Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($activeTsm, 0, '.', ',') }}</h3>

                        </div>
                    </div>
                </div>

                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Ibex  Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($totalIbex, 0, '.', ',') }}</h3>
                            <span class="d-block mb-1">Active Ibex  Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($activeIbex, 0, '.', ',') }}</h3>
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
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Sybrid  Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($totalSybrid, 0, '.', ',') }}</h3>
                            <span class="d-block mb-1">Active Sybrid  Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($activeSybrid, 0, '.', ',') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Abacus Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($totalAbacus, 0, '.', ',') }}</h3>
                            <span class="d-block mb-1">Active Abacus Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($activeAbacus, 0, '.', ',') }}</h3>

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
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png')}}" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total JazzIVR Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($totalJazzIVR, 0, '.', ',') }}</h3>
                            <span class="d-block mb-1">Active JazzIVR Agents</span>
                            <h3 class="card-title text-nowrap mb-2"> {{ number_format($activeJazzIVR, 0, '.', ',') }}</h3>
                        </div>
                    </div>
                </div>



            </div>
        </div>



    </div>
</div>

<div class="row">
    <!-- Bar Charts -->
    <div class="col-xl-6 col-12 mb-4">
        <div class="card">
            <div class="card-header header-elements">
                <h5 class="card-title mb-0">Net Enrollments (Total Sales)</h5>
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
                <h5 class="card-title mb-0">Monthly Active Subscriptions</h5>
                <div class="card-action-element ms-auto py-0">
                    <div class="dropdown">
                        <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current Month</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last Month</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="barChart_1" class="chartjs" data-height="400" height="500" style="display: block; box-sizing: border-box; height: 400px; width: 519px;" width="649"></canvas>
            </div>
        </div>
    </div>

    <!-- Horizontal Bar Charts -->

    <!-- /Horizontal Bar Charts -->

    <!-- Line Charts -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header header-elements">
                <div>
                    <h5 class="card-title mb-0">Monthly Subscription and UnSubscription</h5>
                    <small class="text-muted">Different Between Subscription and UnSubscription Trends</small>
                </div>

            </div>
            <div class="card-body">
                <canvas id="lineChart" class="chartjs" data-height="500" height="625" width="1391" style="display: block; box-sizing: border-box; height: 500px; width: 1112px;"></canvas>
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
        url: '{{ route('superadmin.get-subscription-chart-data') }}',
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
                url: '{{ route('superadmin.getMonthlyActiveSubscriptionChartData') }}'
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


        $(document).ready(function() {
            // Fetch data from the server
            $.ajax({
                url: '{{ route('superadmin.getMonthlySubscriptionUnsubscriptionChartData') }}'
                , type: 'GET'
                , dataType: 'json'
                , success: function(data) {
                    // Update the chart with the fetched data
                    updateLineChart(data);
                }
                , error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        function updateLineChart(data) {
            // Extract necessary data from the fetched response
            var labels = data.labels; // Array of month names
            var subscriptions = data.subscriptions; // Array of corresponding subscription counts
            var unsubscriptions = data.unsubscriptions; // Array of corresponding unsubscription counts

            // Get the chart canvas
            var ctx = document.getElementById('lineChart').getContext('2d');

            // Create a new line chart
            var lineChart = new Chart(ctx, {
                type: 'line'
                , data: {
                    labels: labels
                    , datasets: [{
                        label: 'Subscriptions'
                        , data: subscriptions
                        , borderColor: 'rgba(75, 192, 192, 1)', // Example color for subscriptions
                        borderWidth: 2
                        , fill: false
                    }, {
                        label: 'Unsubscriptions'
                        , data: unsubscriptions
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
        }

    </script>
    <!-- /Line Charts -->

    <!-- Radar Chart -->



    @endsection()
