@extends('company_manager.layout.master')
@include('superadmin.partials.style')
@section('content')



<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Basic Agent Status</li>
                </ol>
            </nav>

        </div>
        @if(Auth::guard('company_manager')->user()->company_id == 11)

        <div class="col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('/assets/img/icons/unicons/wallet.png') }}" alt="Credit Card"
                                class="rounded" />
                        </div>
                    </div>
                    <span class="d-block mb-1">Active Agents (WFO)</span>
                    <h3 class="card-title text-nowrap mb-2"><span id="activeAgents">0</span></h3>
                    <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Live
                        Agents (WFO)</span>
                    <h3 class="card-title text-nowrap mb-2"><span id="liveAgents">0</span></h3>
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
                    <span class="d-block mb-1">Active Agents (WFH)</span>
                    <h3 class="card-title text-nowrap mb-2"><span id="activeAgentsWFH">0</span></h3>
                    <span class="d-block mb-1" style="color: rgb(244, 87, 24);font-weight: bold;">Live
                        Agents (WFH)</span>
                    <h3 class="card-title text-nowrap mb-2"><span id="liveAgentsWFH">0</span></h3>
                </div>
            </div>
        </div>

        @else
        @endif
        <div class="col-xl-12 col-md-12">
            <div class="ms-card">
                <div class="ms-card-body">

                    <table id="myTables" class="display myTables" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>

                                <th>Username</th>
                                <th>Login Status</th>
                                <th>Login Time </th>
                                <th>Emp Code </th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(function () {
        // Initialize the DataTable
        var table = $('#myTables').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('company.manager.agent.data') }}",
                data: function (d) {
                    // You can pass additional data if needed
                }
            },
            columns: [
                { data: 'agent_id', name: 'agent_id' },
                { data: 'username', name: 'username' },
                { data: 'islogin', name: 'islogin' },
                { data: 'today_login_time', name: 'today_login_time' },
                { data: 'emp_code', name: 'emp_code' },
            ],
            // Add the lengthMenu to allow selection of 30, 120, 500 rows per page
            lengthMenu: [ [30, 120, 500], [30, 120, 500] ], // First array is for values, second array for display
            pageLength: 500, // Default page length

            // Add buttons for exporting data
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    className: 'btn btn-primary'
                }
            ]
        });

        // Customize the search input placeholder
        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>

<script>
     function updateDashboardData() {
            $.ajax({
                url: '{{ route('company.manager.ajex') }}', // Replace with your actual route
                type: 'GET',
                success: function(data) {
                    // Update the relevant elements on the dashboard with the returned data
                    $('#liveAgents').text(data.liveAgents);
                    $('#activeAgents').text(data.activeAgents);
                    $('#liveAgentsWFH').text(data.liveAgentsWFH);
                    $('#activeAgentsWFH').text(data.activeAgentsWFH);
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

</script>


@include('superadmin.partials.script');

@endsection


