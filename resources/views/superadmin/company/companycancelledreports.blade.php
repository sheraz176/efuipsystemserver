@extends('superadmin.layout.master')

@section('content')

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="companyFilter">Filter by Company:</label>
            <select id="companyFilter" class="form-select">
                <option value="">All Companies</option>

                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <form method="POST" action="{{ route('superadmin.companies.cancelled-data-export') }}">
                @csrf
            <label for="dateFilter">Filter by Date:</label>
            <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
        </div>
        <div class="col-md-4 mt-4" style="marign-top:10%;">
            <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
        </div>
      </form>
</div>
<table id="dataTable" class="" cellSpacing="0" width="100%">
        <thead>
            <tr>
                <th>Cacellation ID</th>
                <th>Customer MSISDN</th>
                <th>Plan Name</th>
                <th>Product Name</th>
                <th>Amount</th>
                <th>Company Name</th>
                <th>Transaction ID</th>
                <th>Reference ID</th>
                <th>Subscription Date</th>
                <th>UnSubscriotion Date</th>
                <th>Duration</th>
            </tr>
        </thead>
    </table>
</div>

    <script>
    $(document).ready(function() {
       let dataTable= $('#dataTable').DataTable({
            "autoWidth": false,
            "columnDefs": [
                    { "width": "25%", "targets": 2 },
                    { "width": "25%", "targets": 3 },
                    { "width": "10%", "targets": 6 },
                    { "width": "15%", "targets": 8 },
                    { "width": "20%", "targets": 9 },
                ],
	    "lengthMenu": [10, 25, 50, 100,-1], // Set the available page lengths
            "pageLength": 10,
            processing: true,
            serverSide: true,
             ajax: {
                url: "{{ route('superadmin.companies-cancelled-data') }}",
                data: function (d) {
                    d.companyFilter = $('#companyFilter').val();
                    d.dateFilter = $('#dateFilter').val();
                }
            },
            columns: [
            { data: 'unsubscription_id', name: 'unsubscriptions.unsubscription_id' },
            { data: 'subscriber_msisdn', name: 'unsubscriptions.subscriber_msisdn' },
            { data: 'plan_name', name: 'plans.plan_name' },
            { data: 'product_name', name: 'products.product_name' },
            { data: 'transaction_amount', name: 'customer_subscriptions.transaction_amount' },
            { data: 'company_name', name: 'company_profiles.company_name' },
            { data: 'cps_transaction_id', name: 'customer_subscriptions.cps_transaction_id' },
            { data: 'referenceId', name: 'customer_subscriptions.referenceId' },
            { data: 'subscription_time', name: 'customer_subscriptions.subscription_time' },
            { data: 'unsubscription_datetime', name: 'unsubscriptions.unsubscription_datetime' },
            {
                data: 'subscription_duration',
                name: 'subscription_duration',
                render: function (data, type, row) {
                    // Convert seconds to a human-readable format (you may need additional logic)
                    var duration = moment.duration(data, 'seconds');
                    return duration.humanize();
                }
            },

            ],
            dom: 'Blfrtip',
            buttons: [
            { extend: 'copyHtml5', className: 'btn btn-outline-primary' },
            { extend: 'excelHtml5', className: 'btn btn-outline-success' },
            { extend: 'csvHtml5', className: 'btn btn-outline-info' },
            { extend: 'pdfHtml5', className: 'btn btn-outline-danger' }
        ]
        });

        // Initialize datepicker
        $('#dateFilter').daterangepicker({
            opens: 'left', // Adjust the placement as needed
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
        $('#companyFilter, #dateFilter').on('change', function () {
            dataTable.ajax.reload();
        });
    });



    </script>


 @endsection()
