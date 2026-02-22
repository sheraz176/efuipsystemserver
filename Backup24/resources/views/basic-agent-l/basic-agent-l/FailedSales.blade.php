

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
                        <li class="breadcrumb-item active" aria-current="page">Failed Sales Information</li>
                    </ol>
                </nav>
                <div class="ms-panel">


                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <div class="row mb-3">
                            <h6 class="mt-3">Failed Sales Information</h6>

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


                                    <th>Customer Msisdn</th>
                                    <th>Plan ID</th>
                                    <th>Product ID</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Failed Message</th>
                                    <th>Date & Time</th>


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
                    url: "{{ route('basic-agent-l.Failedsucesssales') }}",
                    data: function (d) {
                        var dateFilter = $('#dateFilter').val();
                        if (dateFilter) {
                            d.dateFilter = dateFilter;
                        }
                    }
                },
                columns: [

                    {data: 'accountNumber', name: 'accountNumber'},
                    {data: 'planId', name: 'planId'},
                    {data: 'product_id', name: 'product_id'},
                    {data: 'amount', name: 'amount'},
                    {data: 'transactionId', name: 'transactionId'},
                    {data: 'failedReason', name: 'failedReason'},
                    {data: 'timeStamp', name: 'timeStamp'},

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
