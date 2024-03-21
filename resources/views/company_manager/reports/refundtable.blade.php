@extends('company_manager.layout.master')

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

<div class="row mb-3">
    <div class="col-md-4">
        <form method="POST" action="{{ route('company-manager.ManageRefundedDataExport') }}">
            @csrf
        <label for="dateFilter">Filter by Date:</label>
        <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
    </div>
      <div class="col-md-4 mt-4" style="marign-top:10%;">
        <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
      </div>
     </form>
    <div class="col-md-4">
        <label for="msisdn">Search by Mobile Number:</label>
        <input type="text" id="msisdn" class="form-control" placeholder="Enter MSISDN">
    </div>
</div>

<table id="dataTable" class="table" cellspacing="0" width="100%">
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

<script>
    $(document).ready(function() {
        let dataTable_new = $('#dataTable').DataTable({
            "autoWidth": false,
            "columnDefs": [
                { "width": "0%", "targets": 0 },
                { "width": "5%", "targets": 1 },
                { "width": "10%", "targets": 2 },
                { "width": "15%", "targets": 3 },
                { "width": "10%", "targets": 5 },
                { "width": "15%", "targets": 7 },
                { "width": "15%", "targets": 8 },
                { "width": "15%", "targets": 9 },
                { "width": "15%", "targets": 9 },
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('company-manager.manage-refunds.getRefundData') }}",
                data: function(d) {
                    d.dateFilter = $('#dateFilter').val();
                    d.msisdn = $('#msisdn').val();
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

        $('#dateFilter').daterangepicker({
            opens: 'left', // Adjust the placement as needed
            startDate: moment().startOf('month'),
            endDate: moment(),
            maxDate: moment(), // Disable future dates
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

        // Apply the filters on change
        $('#dateFilter, #msisdn').on('change', function() {
            dataTable_new.ajax.reload();
        });
    });

    // Manually trigger the toast
    $(document).ready(function() {
        $('.bs-toast').toast('show');
    });
    $(document).ready(function() {
        $('.btn-close-2').toast('show');
    });
</script>

@endsection
