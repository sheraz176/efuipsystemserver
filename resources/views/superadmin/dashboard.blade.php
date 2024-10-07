@extends('superadmin.layout.master')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif


    <h4 class=""><span class="text-muted fw-light">Company Performance/ </span> Daily Lifes Secured & Total Sales</h4>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">

            <div class="row">
                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                        class="rounded" />
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
                            <h3 class="card-title text-nowrap mb-2">
                                {{ number_format($currentYearSubscriptionCount, 0, '.', ',') }}</h3>
                            {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                        class="rounded" />
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
                            <h3 class="card-title mb-2">{{ number_format($currentMonthSubscriptionCount, 0, '.', ',') }}
                            </h3>
                            {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/chart.png') }}" alt="Credit Card"
                                        class="rounded" />
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
                            <h3 class="card-title text-nowrap mb-2">
                                {{ number_format($todaySubscriptionCount, 0, '.', ',') }}</h3>
                            {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                        class="rounded" />
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
                            <span class="fw-medium d-block mb-1">Current Year Total Sales</span>
                            <h3 class="card-title mb-2">{{ number_format($yearlyTransactionSum, 0, '.', ',') }}</h3>
                            {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/chart.png') }}" alt="Credit Card"
                                        class="rounded" />
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
                            <h3 class="card-title text-nowrap mb-2">
                                {{ number_format($monthlyTransactionSum, 0, '.', ',') }}</h3>
                            {{-- <small class="text-danger fw-medium"><i class="bx bx-down-arrow-alt"></i> -14.82%</small> --}}
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                        class="rounded" />
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
                            <span class="fw-medium d-block mb-1">Today's Total Sales</span>
                            <h3 class="card-title mb-2">{{ number_format($dailyTransactionSum, 0, '.', ',') }}</h3>
                            {{-- <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.14%</small> --}}
                        </div>
                    </div>
                </div>


            </div>


        </div>

    </div>
    </div>

    <h4 class=""><span class="text-muted fw-light">Overall Agents Activity/ </span> Live & Total Registered</h4>

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">

            <div class="row">


                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card" class="rounded" />
                                </div>
                                 <!-- Toggle Button -->
                             <div class="text-center">
                                <button id="toggleCounts" class="btn btn-primary btn-sm">Show WFH Counts</button>
                            </div>
                            </div>

                            <!-- Default view showing TSM counts -->
                            <div id="tsmCounts">
                                <span class="d-block mb-1">Active Tsm Agents</span>
                                <h3 class="card-title text-nowrap mb-2"><span id="totalTsm">0</span></h3>

                                <span class="d-block mb-1" style="color: rgb(244, 87, 24); font-weight: bold;">Live Tsm Agents</span>
                                <h3 class="card-title text-nowrap mb-2"><span id="activeTsm">0</span></h3>
                            </div>

                            <!-- WFH counts hidden initially -->
                            <div id="wfhCounts" style="display: none;">
                                <span class="d-block mb-1">Active Tsm Agents (WFH)</span>
                                <h3 class="card-title text-nowrap mb-2"><span id="totalTsmWfh">0</span></h3>

                                <span class="d-block mb-1" style="color: rgb(244, 87, 24); font-weight: bold;">Live Tsm Agents (WFH)</span>
                                <h3 class="card-title text-nowrap mb-2"><span id="activeTsmWfh">0</span></h3>
                            </div>


                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Active Ibex Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="totalIbex">0</span></h3>
                            <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Live Ibex
                                Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="activeIbex">0</span></h3>
                        </div>
                    </div>
                </div>



                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Active Sybrid Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="totalSybrid">0</span></h3>
                            <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Live Sybrid
                                Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="activeSybrid">0</span></h3>
                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Active Abacus Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="totalAbacus">0</span></h3>
                            <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Live Abacus
                                Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="activeAbacus">0</span></h3>

                        </div>
                    </div>
                </div>

                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Active JazzIVR Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="totalJazzIVR">0</span></h3>
                            <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Live JazzIVR
                                Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="activeJazzIVR">0</span></h3>
                        </div>
                    </div>
                </div>
                <div class="col-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Active Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="totalactive">0</span></h3>
                            <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Total Live
                                Agents</span>
                            <h3 class="card-title text-nowrap mb-2"><span id="totallive">0</span></h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <h4 class=""><span class="text-muted fw-light">Net Enrollment Pattern </span>(Daily,Weekly,Monthly)</h4>

    <div class="row">
        <!-- Bar Charts -->
        <div class="col-xl-6 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Net Enrollments (Total Sales)</h5>
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
                    <canvas id="barChart" class="chartjs" data-height="400" height="500"
                        style="display: block; box-sizing: border-box; height: 400px; width: 519px;"
                        width="649"></canvas>
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
                            <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                                aria-expanded="false"><i class="bx bx-calendar"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center">Today</a></li>
                                <li><a href="javascript:void(0);"
                                        class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7
                                        Days</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30
                                        Days</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current
                                        Month</a></li>
                                <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last
                                        Month</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="barChart_1" class="chartjs" data-height="400" height="500"
                        style="display: block; box-sizing: border-box; height: 400px; width: 519px;"
                        width="649"></canvas>
                </div>
            </div>
        </div>

        <!-- Horizontal Bar Charts -->

        <!-- /Horizontal Bar Charts -->
        <h4 class=""><span class="text-muted fw-light">Overall Subscription and UnSubscription </span>Pattern</h4>

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
                    <canvas id="lineChart" class="chartjs" data-height="500" height="625" width="1391"
                        style="display: block; box-sizing: border-box; height: 500px; width: 1112px;"></canvas>
                </div>
            </div>
        </div>

        <h4 class=""><span class="text-muted fw-light">Hourly Net Enrollment</span> (Total Present Agents, Total
            MSISDN , Average)</h4>

        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements d-flex align-items-center">
                    <!-- Company Filter Dropdown and Gross Productivity Green Box (aligned next to each other) -->
                    <div class="d-flex align-items-center">
                        <!-- Company Filter Dropdown -->
                        <div class="me-3">
                            <label for="companyFilters">Filter by Company:</label>
                            <select id="companyFilters" class="form-select">
                                <option value="11">TSM</option>
                                <option value="12">Sybrid</option>
                                <option value="1">Ibex International</option>
                                <option value="2">Abacus Consultation</option>
                            </select>
                        </div>

                        <!-- Gross Productivity Green Box (immediately next to filter) -->
                        <div id="last-hour-productivity" class="alert alert-success mb-0"
                            style="display: none; margin-top: 2%">
                            Gross Productivity: <span id="productivity-value"></span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table" id="data-table">
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th>Total Present Agents</th>
                                <th>Total MSISDN</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <h4 class=""><span class="text-muted fw-light">Recusive Charging</span> Performance</h4>

  <div class="col-xl-8 col-12 mb-4">
    <div class="card">
        <div class="card-header header-elements">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="me-2">
                        <label for="causeFilter">Filter by Causes:</label>
                        <select id="causeFilter" class="form-select">
                            <option value="">All</option>
                            <option value="Process service request successfully.">Success Causes</option>
                            <option value="Insufficient balance.">Failure Causes</option>
                        </select>
                    </div>
                    <div>
                        <label for="timecauseFilter">Filter by Time Period:</label>
                        <select id="timecauseFilter" class="form-select">
                            <option value="today">Today</option>
                            <option value="last7days">Last 7 Days</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <canvas id="barChart_Recusive" class="chartjs" data-height="500"></canvas>
        </div>
    </div>
</div>

<div class="col-4 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                    <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                        class="rounded" />
                </div>
            </div>
            <span class="fw-medium d-block mb-1">Today Recusive Charging Count</span>
            <h3 class="card-title mb-2">{{ number_format($TodayRecusiveChargingCount, 0, '.', ',') }}</h3>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i>
                +{{ number_format($TodayRecusiveChargingCount, 0, '.', ',') }}%</small>
        </div>
        <hr>
        <div class="card-body">

            <span class="fw-medium d-block mb-1">Last Month Recusive Charging Count</span>
            <h3 class="card-title mb-2">{{ number_format($LastMonthRecusiveChargingCount, 0, '.', ',') }}</h3>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i>
                +{{ number_format($LastMonthRecusiveChargingCount, 0, '.', ',') }}%</small>
        </div>
        <hr>
        <div class="card-body">

            <span class="fw-medium d-block mb-1">Total Recusive Charging Count</span>
            <h3 class="card-title mb-2">{{ number_format($TotalRecusiveChargingCount, 0, '.', ',') }}</h3>
            <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i>
                +{{ number_format($TotalRecusiveChargingCount, 0, '.', ',') }}%</small>
        </div>
    </div>

</div>



        <!-- Net Enrollment Charts -->
        <h4 class=""><span class="text-muted fw-light">Overall Net Enrollment</span> Performance</h4>

        <div class="col-xl-8 col-12 mb-4">
            <div class="card">
                <div class="card-header header-elements">
                    <div class="d-flex justify-content-between align-items-center">
                        {{-- <h5 class="card-title mb-0">Net Enrollment </h5> --}}
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="me-2">
                                <label for="companyFilter">Filter by Company:</label>
                                <select id="companyFilter" class="form-select">
                                    <option value="">All Companies</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="timeFilter">Filter by Time Period:</label>
                                <select id="timeFilter" class="form-select">
                                    <option value="daily">Daily</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="last7days">Last 7 Days</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="hourly">Hourly</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="barChart_netenrollment" class="chartjs" data-height="500"></canvas>
                </div>
            </div>
        </div>
        <div class="col-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                class="rounded" />
                        </div>
                    </div>
                    <span class="fw-medium d-block mb-1">Total Net Enrollment Count</span>
                    <h3 class="card-title mb-2">{{ number_format($NetEnrollmentCount, 0, '.', ',') }}</h3>
                    <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i>
                        +{{ number_format($NetEnrollmentCount, 0, '.', ',') }}%</small>
                </div>
            </div>
        </div>

        <div class="col-4 " style="margin-left: 67%; margin-top:-25%;">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card"
                                class="rounded" />
                        </div>
                    </div>
                    <span class="fw-medium d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Total Live Net
                        Enrollment Revenue</span>
                    <h3 class="card-title mb-2"><span id="netentrollmentrevinus"> </span></h3>

                </div>
            </div>
        </div>


<!-- Add JavaScript to toggle between TSM and WFH counts -->
<script>
    document.getElementById('toggleCounts').addEventListener('click', function() {
        // Toggle visibility of TSM and WFH counts
        const tsmCounts = document.getElementById('tsmCounts');
        const wfhCounts = document.getElementById('wfhCounts');
        const toggleButton = document.getElementById('toggleCounts');

        if (tsmCounts.style.display === 'none') {
            tsmCounts.style.display = 'block';
            wfhCounts.style.display = 'none';
            toggleButton.textContent = 'Show WFH Counts';
        } else {
            tsmCounts.style.display = 'none';
            wfhCounts.style.display = 'block';
            toggleButton.textContent = 'Show TSM Counts';
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
                    url: '{{ route('superadmin.get-subscription-chart-data') }}',
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
                    url: '{{ route('superadmin.getMonthlyActiveSubscriptionChartData') }}',
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


            $(document).ready(function() {
                // Fetch data from the server
                $.ajax({
                    url: '{{ route('superadmin.getMonthlySubscriptionUnsubscriptionChartData') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Update the chart with the fetched data
                        updateLineChart(data);
                    },
                    error: function(error) {
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
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Subscriptions',
                            data: subscriptions,
                            borderColor: 'rgba(75, 192, 192, 1)', // Example color for subscriptions
                            borderWidth: 2,
                            fill: false
                        }, {
                            label: 'Unsubscriptions',
                            data: unsubscriptions,
                            borderColor: 'rgba(255, 99, 132, 1)', // Example color for unsubscriptions
                            borderWidth: 2,
                            fill: false
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
        <!-- /Line Charts -->

        <!-- Net Enrollment Chart -->
        <script>
            $(document).ready(function() {
                var ctx = document.getElementById('barChart_netenrollment').getContext('2d');
                var barChart = new Chart(ctx, {
                    type: 'bar',
                    data: {}, // Initial data
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                function fetchChartData(companyId, timePeriod) {
                    $.ajax({
                        url: '{{ route('chart.data') }}',
                        method: 'GET',
                        data: {
                            company_id: companyId,
                            time_period: timePeriod
                        },
                        success: function(response) {
                            barChart.data = response.data;
                            barChart.update();
                        }
                    });
                }


                // Initial load
                fetchChartData('', 'daily');

                // Update chart on filter change
                $('#companyFilter, #timeFilter').change(function() {
                    var companyId = $('#companyFilter').val();
                    var timePeriod = $('#timeFilter').val();
                    fetchChartData(companyId, timePeriod);
                });
            });


            function updateStats() {
                $.ajax({
                    url: '{{ route('dashboard.stats') }}',
                    type: 'GET',
                    success: function(data) {
                        $('#totalTsm').text(data.totalTsm);
                        $('#activeTsm').text(data.activeTsm);

                        $('#totalTsmWfh').text(data.totalTsmWfh);
                        $('#activeTsmWfh').text(data.activeTsmWfh);

                        $('#totalIbex').text(data.totalIbex);
                        $('#activeIbex').text(data.activeIbex);

                        $('#totalAbacus').text(data.totalAbacus);
                        $('#activeAbacus').text(data.activeAbacus);

                        $('#totalSybrid').text(data.totalSybrid);
                        $('#activeSybrid').text(data.activeSybrid);

                        $('#totalJazzIVR').text(data.totalJazzIVR);
                        $('#activeJazzIVR').text(data.activeJazzIVR);

                        $('#totalactive').text(data.totalactive);
                        $('#totallive').text(data.totallive);

                        $('#netentrollmentrevinus').text(data.netentrollmentrevinus);


                    }
                });
            }
            // Call updateStats function every 10 seconds
            setInterval(updateStats, 10000);
            // Initial call
            updateStats();
        </script>

<script>
    $(document).ready(function() {
        var defaultCompanyId = $('#companyFilters').val(); // Get the default selected company ID

        function fetchTableData(companyId = '') {
            $.ajax({
                url: '{{ route('superadmin.revinuechart') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    company_id: companyId // Pass the selected company ID to the server
                },
                success: function(data) {
                    console.log('Data received:', data); // Debugging
                    updateTable(data); // Update the table with the data
                    updateGrossProductivity(data); // Update gross productivity
                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        // Fetch table data on page load with default company ID
        fetchTableData(defaultCompanyId);

        // Fetch table data whenever the company filter changes
        $('#companyFilters').change(function() {
            var companyId = $(this).val();
            fetchTableData(companyId);
        });

        // Update table function
        function updateTable(data) {
            var tableBody = $('#data-table tbody');
            tableBody.empty(); // Clear existing table rows

            // Populate table with hourly MSISDN, total average, and productivity data
            data.labels.forEach(function(label, index) {
                // Format the time for display (convert to AM/PM format)
                var date = new Date(label);
                var hours = date.getHours();
                var minutes = date.getMinutes();
                var suffix = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12; // Convert 0 to 12
                var formattedTime = hours + ':' + (minutes < 10 ? '0' : '') + minutes + ' ' + suffix;

                // Determine arrow direction for average (>= 1.0 is up, < 1.0 is down)
                var avgArrow = data.total_avg[index] >= 1.0 ?
                    '<i class="bx bx-up-arrow-alt" style="color: green;"></i>' :
                    '<i class="bx bx-down-arrow-alt" style="color: red;"></i>';

                // Append row to the table
                var row = `
                    <tr>
                        <td>${formattedTime}</td>
                        <td>${data.total_present_agent[index]}</td> <!-- Live agent count for the specific hour -->
                        <td>${data.total_msisdn[index]}</td> <!-- Sales (MSISDN) per hour -->
                        <td>
                            ${data.total_avg[index]}
                            ${avgArrow} <!-- Average arrow with icon -->
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
        }

        // Update Gross Productivity
        function updateGrossProductivity(data) {
            if (data.gross_productivity) {
                // Update the green box with the gross productivity value (sum of averages)
                $('#productivity-value').text(data.gross_productivity.toFixed(2)); // Round to 2 decimal places
                $('#last-hour-productivity').show(); // Show the green box
            }
        }
    });
</script>


        <script>
            $(document).ready(function() {
                let chart; // To store the chart instance

                // Function to fetch data and update the chart
                function fetchRecusiveChargingData() {
                    var causeFilter = $('#causeFilter').val();
                    var timePeriodFilter = $('#timecauseFilter').val();

                    $.ajax({
                        url: '{{ route('superadmin.recusive.charging') }}',
                        type: 'GET',
                        data: {
                            cause: causeFilter,
                            time_period: timePeriodFilter,
                        },
                        success: function(response) {
                            // Extract labels and counts from the response
                            let labels = response.map(item => item
                            .label); // Day name, Month name, Hour, etc.
                            let data = response.map(item => item.count); // Counts for each group

                            // If chart already exists, destroy it to avoid overlay
                            if (chart) {
                                chart.destroy();
                            }

                            // Create a new chart with the updated data
                            var ctx = document.getElementById('barChart_Recusive').getContext('2d');
                            chart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Recusive Charging Count',
                                        data: data,
                                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
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
                        },
                        error: function(error) {
                            console.log('Error fetching data', error);
                        }
                    });
                }

                // Event listeners for the filters
                $('#causeFilter, #timecauseFilter').on('change', function() {
                    fetchRecusiveChargingData();
                });

                // Initial chart load
                fetchRecusiveChargingData();
            });
        </script>
    @endsection()
