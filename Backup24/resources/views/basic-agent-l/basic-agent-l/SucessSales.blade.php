
 @extends('basic-agent-l.layout.master')
@include('superadmin.partials.style')
@section('content')




    <div class="ms-content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb pl-0">
                        <li class="breadcrumb-item"><a href="{{ route('basic-agent-l.dashboard') }}"><i class="material-icons"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('basic-agent-l.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Active Policies Sold & Active</li>
                    </ol>
                </nav>
                <div class="ms-panel">


                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <div class="row mb-3">
                            <h6 class="mt-3">Active Policies Sold & Active</h6>

                        </div>
                        <div class="col-md-6">

                        </div>

                        <div class="col-md-4 mt-6" style="margin-left: -14%">

                        </div>


                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-md-12">
                <div class="ms-card">
                    <div class="ms-card-body">

                        <table id="myTables" class="display myTables" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Subscription ID</th>
                                    <th>Customer Msisdn</th>
                                    <th>Plan Name</th>
                                    <th>Product Name</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Subscription Time</th>
                                    <th>Policy Status</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(function () {
            // Initialize the date range picker


            var table = $('#myTables').DataTable({
                responsive: true,

                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('basic-agent-l.sucesssales') }}",
                    data: function (d) {
                        var dateFilter = $('#dateFilter').val();
                        if (dateFilter) {
                            d.dateFilter = dateFilter;
                        }
                    }
                },
                columns: [
                    {data: 'subscription_id', name: 'subscription_id'},
                    {data: 'subscriber_msisdn', name: 'subscriber_msisdn'},
                    {data: 'plan_name', name: 'plan_name'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'transaction_amount', name: 'transaction_amount'},
                    {data: 'cps_transaction_id', name: 'cps_transaction_id'},
                    {data: 'subscription_time', name: 'subscription_time'},
                    {data: 'policy_status', name: 'policy_status'},
                ]
            });

            var search_input = document.querySelectorAll('.dataTables_filter input');
            search_input.forEach(Element => {
                Element.placeholder = 'Search by name';
            });
        });
    </script>



@include('superadmin.partials.script')





 @endsection
