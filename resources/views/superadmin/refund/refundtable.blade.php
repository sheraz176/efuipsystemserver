@extends('superadmin.layout.master')
@include('superadmin.partials.style')
<link href="{{asset('newdes/assets/css/toastr.min.css')}}" rel="stylesheet">

@section('content')

@if(session('success'))
<div class="bs-toast toast toast-placement-ex m-2 fade bg-success top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-medium">Bootstrap</div>
        <small>11 mins ago</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">{{ session('success') }}</div>
</div>
@endif

@if(session('error'))
<!-- Error Toast -->
<div class="bs-toast toast toast-placement-ex m-2 fade bg-danger top-0 end-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-medium">Error</div>
        <small>Now</small>
        <button type="button" class="btn-close-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">{{ session('error') }}</div>
</div>
@endif


<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Deshboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Refunds List</li>
                </ol>
            </nav>
            <div class="ms-panel">

                <form method="POST" action="{{ route('superadmin.ManageRefundedDataExport') }}">
                    @csrf
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row mb-3">
                    </div>
                    <div class="col-md-2">


                    </div>
                        <div class="col-md-4">
                            <label for="msisdn">Search by Mobile Number:</label>
                            <input type="text" id="msisdn" class="form-control" placeholder="Enter MSISDN">
                        </div>
                    <div class="col-md-4">

                        <label for="dateFilter">Filter by Date:</label>
                        <input type="text" id="dateFilter" name="dateFilter" class="form-control " placeholder="Select date range">
                    </div>

                    <div class="col-md-2 mt-8" style="margin-top: 2%">
                        <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>

                    </div>



                </div>
                </form>
            </div>
        </div>
        <div class="col-xl-12 col-md-12">
            <div class="ms-card">
                <div class="ms-card-body">

                    <table id="myTables" class="display myTables" style="width:100%">
                        <thead>
                            <tr>
                                <th>Subscription ID</th>
                                <th>Customer MSISDN</th>
                                <th>Plan Name</th>
                                <th>Product Name</th>
                                <th>Amount</th>
                                <th>Company Name</th>
                                <th>Agent Name</th>
                                <th>Next Charging Date</th>
                                <th>Subscription Date</th>
                                <th>Free Look Period</th>
                                <th>Action</th>
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
        // Initialize the date range picker
        $('#dateFilter').daterangepicker({
            opens: 'left',
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' to ',
                applyLabel: 'Apply',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom'
            }
        });

        // Update the input field when date range is applied
        $('#dateFilter').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            table.ajax.reload();
        });

        // Clear the input field when date range is canceled
        $('#dateFilter').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
            table.ajax.reload();
        });

        var table = $('#myTables').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('manage-refunds.getRefundData') }}",
                data: function (d) {
                    var dateFilter = $('#dateFilter').val();
                    if (dateFilter) {
                        d.dateFilter = dateFilter;
                    }
                    var msisdn = $('#msisdn').val();
                    if (msisdn) {
                        d.msisdn = msisdn;
                    }
                }
            },
            columns: [
                { data: 'subscription_id', name: 'subscription_id' },
                { data: 'subscriber_msisdn', name: 'subscriber_msisdn' },
                { data: 'plan_name', name: 'plan_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'transaction_amount', name: 'transaction_amount' },
                { data: 'company_name', name: 'company_name' },
                { data: 'sales_agent', name: 'sales_agent' },
                { data: 'recursive_charging_date', name: 'recursive_charging_date' },
                { data: 'subscription_time', name: 'subscription_time' },
                { data: 'grace_period_time', name: 'grace_period_time' },
                {
                    data: 'subscription_id', // Assuming 'subscription_id' is the ID of the subscription
                    name: 'action',
                    render: function(data, type, full, meta) {
                        return '<a href="{{ route('refunded.unsubscribe-now', '') }}/' + data + '" class="btn btn-danger">Refund</a>';
                    }
                },
            ],
        });
        $('#msisdn').on('change', function () {
            table.ajax.reload();
        });
        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>


<script>
    function toastSuccess() {
        // alert('hi');
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.success('Customer unsubscribed successfully.', 'Successfull !');
    }

    function toastdanger() {
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.error('Invalid response from API', 'Some thing Wrong !');
    }
</script>
<script>
    $(document).ready(function() {

        var created = "{{ Session::get('success') }}";
        if (created) {
            toastSuccess();
        }

        var error = "{{ Session::get('error') }}";
        if (error) {
            toastdanger();
        }

    });

</script>

<script src="{{asset('newdes/assets/js/toastr.min.js')}}"> </script>
<script src="{{asset('newdes/assets/js/toast.js')}}"> </script>


@include('superadmin.partials.script')

@endsection
