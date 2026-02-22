@extends('company_manager.layout.master')

@section('content')
    <h4 class=""><span class="text-muted fw-light">Company Performance/</span> Daily Lifes Secured & Total Sales</h4>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="fw-medium d-block mb-1" style="color: red">Live Login Agents (Our Company)</span>
                        <h3 class="card-title mb-2"><span id="liveAgents">0</span></h3>
                        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i>
                            +<span id="liveAgents">0</span>%</small>
                    </div>
                </div>
            </div>

            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="fw-medium d-block mb-1">Total Active Agents (Our Company)</span>
                        <h3 class="card-title mb-2"><span id="activeAgents">0</span></h3>
                        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i>
                            +<span id="activeAgents">0</span>%</small>
                    </div>
                </div>
            </div>

            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="d-block mb-1">Current Year Lifes Secured</span>
                        <h3 class="card-title text-nowrap mb-2">
                            <span id="currentYearSubscriptionCount">0</span>
                        </h3>

                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="fw-medium d-block mb-1">Current Month Total Lifes Secured</span>
                        <h3 class="card-title mb-2"><span id="currentMonthSubscriptionCount">0</span></h3>

                    </div>
                </div>
            </div>


            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/chart.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="d-block mb-1">Today's Total Lifes Secured</span>
                        <h3 class="card-title text-nowrap mb-2">
                            <span id="todaySubscriptionCount">0</span>
                        </h3>

                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="fw-medium d-block mb-1">Current Year Total Sales(Company)</span>
                        <h3 class="card-title mb-2">
                            <span id="yearlyTransactionSum">0</span>
                        </h3>

                    </div>
                </div>
            </div>


            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/chart.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="d-block mb-1">Current Month's Total Sales(Company)</span>
                        <h3 class="card-title text-nowrap mb-2">
                            <span id="monthlyTransactionSum">0</span>
                        </h3>

                    </div>
                </div>
            </div>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                    class="rounded" />
                            </div>

                        </div>
                        <span class="fw-medium d-block mb-1">Today's Total Sales (Company)</span>
                        <h3 class="card-title mb-2">
                            <span id="dailyTransactionSum">0</span>
                        </h3>

                    </div>
                </div>
            </div>

            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">


                        </div>
                        <span class="fw-medium d-block mb-1">NetEnrollments Last 30 (Days)</span>

                        <canvas id="netEnrollmentChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                        </div>
                        <span class="fw-medium d-block mb-1">Refunds Last 30 (Days)</span>

                        <canvas id="refundedCustomersChart" width="400" height="200"></canvas>
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
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false"><i class="bx bx-calendar"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="today">Today</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="yesterday">Yesterday</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="last_7_days">Last 7 Days</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="last_30_days">Last 30 Days</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="current_month">Current Month</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="last_month">Last Month</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                        data-range="this_year">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="barChart" class="chartjs" data-height="400" height="400"
                        style="display: block; box-sizing: border-box; height: 400px; width: 519px;"
                        width="649"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Active Customers</h5>
                    <div class="card-action-element ms-auto py-0">
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="barChart_1" class="chartjs" data-height="400" height="400"
                        style="display: block; box-sizing: border-box; height: 400px; width: 519px;"
                        width="649"></canvas>
                </div>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateDashboardData() {
            $.ajax({
                url: '{{ route('company.manager.ajex') }}', // Replace with your actual route
                type: 'GET',
                success: function(data) {
                    // Update the relevant elements on the dashboard with the returned data
                    $('#liveAgents').text(data.liveAgents);
                    $('#todaySubscriptionCount').text(data.todaySubscriptionCount);
                    $('#activeAgents').text(data.activeAgents);
                    $('#currentMonthSubscriptionCount').text(data.currentMonthSubscriptionCount);
                    $('#currentYearSubscriptionCount').text(data.currentYearSubscriptionCount);
                    $('#dailyTransactionSum').text(data.dailyTransactionSum);
                    $('#monthlyTransactionSum').text(data.monthlyTransactionSum);
                    $('#yearlyTransactionSum').text(data.yearlyTransactionSum);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                }
            });
        }

        // Call this function periodically to update the dashboard data, e.g., every 30 seconds
        setInterval(updateDashboardData, 30000);

        // Or call it when the page loads
        $(document).ready(function() {
            updateDashboardData();
        });



        const ctx = document.getElementById('netEnrollmentChart').getContext('2d');
        $.ajax({
            url: '{{ route('company.manager.netenrollment.chart') }}',
            method: 'GET',
            success: function(response) {
                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.dates, // Dates for the last 30 days
                        datasets: [{
                            label: 'Net Enrollment',
                            data: response.enrollments, // Enrollment counts
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                type: 'category', // Use 'category' for daily data
                                time: {
                                    unit: 'day', // Display by day
                                    tooltipFormat: 'YYYY-MM-DD' // Format for the tooltip
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    </script>

    <script>
        const ctxx = document.getElementById('refundedCustomersChart').getContext('2d');
        $.ajax({
            url: '{{ route('company.manager.refundedcustomers.chart') }}',
            method: 'GET',
            success: function(response) {
                const chart = new Chart(ctxx, {
                    type: 'line',
                    data: {
                        labels: response.dates, // Dates for the last 30 days
                        datasets: [{
                            label: 'Refunded Customers',
                            data: response.refunds, // Refund counts
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                type: 'category', // Use 'category' for daily data
                                time: {
                                    unit: 'day', // Display by day
                                    tooltipFormat: 'YYYY-MM-DD' // Format for the tooltip
                                }
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
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
                data: {
                    time_range: timeRange
                }, // Pass the time range parameter
                dataType: 'json',
                success: function(data) {
                    // Update the chart with the fetched data
                    updateChart(data);
                },
                error: function(error) {
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
            var labels = data.labels; // Array of date labels
            var values = data.values; // Array of corresponding subscription counts

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
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Example color
                        borderColor: 'rgba(75, 192, 192, 1)', // Example color
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
                url: '{{ route('companymanager.getMonthlyActiveSubscriptionChartData') }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Update the chart with the fetched data
                    updateChart_2(data, 'barChart_1'); // Pass chart ID as an argument
                },
                error: function(error) {
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
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Active Subscriptions',
                        data: values,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Example color
                        borderColor: 'rgba(75, 192, 192, 1)', // Example color
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
    </script>
@endsection()
