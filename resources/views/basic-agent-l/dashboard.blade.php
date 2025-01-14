@extends('basic-agent-l.layout.master')

@section('content')
@if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <div class="col-lg-8 mb-4 order-0">
                  <div class="card">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h5 class="card-title text-primary">Welcome {{ session('agent')->username }}🎉</h5>

                          <p class="mb-4">
                            You have done <span class="fw-medium">72%</span> more sales today. Check your new badge in
                            your profile.
                          </p>

                          <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img
                            src="{{ asset('/assets/img/illustrations/man-with-laptop-light.png')}}"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 order-1">
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img
                                src="{{ asset('/assets/img/icons/unicons/chart-success.png')}}"
                                alt="chart success"
                                class="rounded" />
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt3"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                              </div>
                            </div>
                          </div>
                          <span class="fw-medium d-block mb-1">Today Sales</span>
                          <h3 class="card-title mb-2"><span id="todaySalesCount">0</span> </h3>
                          <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +10.50%</small>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img
                                src="{{ asset('/assets/img/icons/unicons/wallet-info.png')}}"
                                alt="Credit Card"
                                class="rounded" />
                            </div>
                            <div class="dropdown">
                              <button
                                class="btn p-0"
                                type="button"
                                id="cardOpt6"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                <a class="dropdown-item" href="javascript:void(0);">View More</a>
                                <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                              </div>
                            </div>
                          </div>
                          <span>Current Month</span>
                          <h3 class="card-title text-nowrap mb-1"><span id="currentMonthTotalCount">0</span></h3>
                          <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Total Revenue -->
                <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                  <div class="card">
                    <div class="row row-bordered g-0">
                      <div class="col-md-8">
                        {{-- <h5 class="card-header m-0 me-2 pb-3">Total Life Secured </h5>
                        <div id="totalRevenueChart" class="px-2"></div> --}}
                      </div>
                      <div class="col-md-4">
                        <div class="card-body">
                          <div class="text-center">
                            <div class="dropdown">
                              <button
                                class="btn btn-sm btn-outline-primary dropdown-toggle"
                                type="button"
                                id="growthReportId"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                2025
                              </button>
                              <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">

                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="growthChart"></div>
                        <div class="text-center fw-medium pt-3 mb-2">62% EFU LIFE Inclusive Growth</div>

                        <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                          <div class="d-flex">
                            <div class="me-2">
                              <span class="badge bg-label-primary p-2"><i class="bx bx-wallet text-primary"></i></span>
                            </div>
                            {{-- <div class="d-flex flex-column">
                              <small>2024</small>
                              <h6 class="mb-0">32.5k</h6>
                            </div> --}}
                          </div>
                          {{-- <div class="d-flex">
                            <div class="me-2">
                              <span class="badge bg-label-info p-2"><i class="bx bx-wallet text-info"></i></span>
                            </div>
                            <div class="d-flex flex-column">
                              <small>2021</small>
                              <h6 class="mb-0">41.2k</h6>
                            </div>
                          </div> --}}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--/ Total Revenue -->
                <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                  <div class="row">

                    <!-- </div>
    <div class="row"> -->
                    <div class="col-12 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                            <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                              <div class="card-title">
                                <h5 class="text-nowrap mb-2">{{ session('agent')->first_name }}'s Total This Year Sales</h5>
                                <span class="badge bg-label-warning rounded-pill">Year 2025</span>
                              </div>
                              <div class="mt-sm-auto">
                                <small class="text-success text-nowrap fw-medium"
                                  ><i class="bx bx-chevron-up"></i> 68.2%</small
                                >
                                <h3 class="mb-0"> <span id="currentYearTotal">0</span></h3>
                              </div>
                            </div>
                            <div id="profileReportChart"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>





            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function () {
                    function fetchDashboardData() {
                        $.ajax({
                            url: "{{ route('agent.dashboard.data') }}",
                            method: "GET",
                            success: function (response) {
                                if (response.status === 'success') {
                                    // Update the dashboard with received data
                                    $('#todaySalesCount').text(response.data.todaySalesCount);
                                    $('#currentMonthTotalCount').text(response.data.currentMonthTotalCount);
                                    $('#currentYearTotal').text(response.data.currentYearTotal);
                                } else {
                                    alert('Error: ' + response.message);
                                }
                            },
                            error: function (xhr) {
                                if (xhr.status === 401) {
                                    alert('Session Expired. Please re-login.');
                                    window.location.reload();
                                } else {
                                    alert('Something went wrong. Please try again later.');
                                }
                            }
                        });
                    }

                    // Fetch data on page load
                    fetchDashboardData();

                    // Optionally, set up an interval to refresh data periodically
                    setInterval(fetchDashboardData, 60000); // Refresh every 60 seconds
                });
            </script>


@endsection()
