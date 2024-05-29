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
<table id="myTables" class="display myTables" cellSpacing="0" width="100%">
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



<script type="text/javascript">
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
                url: "{{ route('superadmin.companies-cancelled-data') }}",
                data: function (d) {
                    var dateFilter = $('#dateFilter').val();
                    if (dateFilter) {
                        d.dateFilter = dateFilter;
                    }
                    var companyFilter = $('#companyFilter').val();
                    if (companyFilter) {
                        d.companyFilter = companyFilter;
                    }
                }
            },
            columns: [
            { data: 'unsubscription_id', name: 'unsubscription_id' },
            { data: 'subscriber_msisdn', name: 'subscriber_msisdn' },
            { data: 'plan_name', name: 'plan_name' },
            { data: 'product_name', name: 'product_name' },
            { data: 'transaction_amount', name: 'transaction_amount' },
            { data: 'company_name', name: 'company_name' },
            { data: 'cps_transaction_id', name: 'cps_transaction_id' },
            { data: 'referenceId', name: 'referenceId' },
            { data: 'subscription_time', name: 'subscription_time' },
            { data: 'unsubscription_datetime', name: 'unsubscription_datetime' },
            {
                data: 'subscription_duration',
                name: 'subscription_duration',
                render: function (data, type, row) {
                    // Convert seconds to a human-readable format (you may need additional logic)
                    var duration = moment.duration(data, 'seconds');
                    return duration.humanize();
                }
            },

            ]
        });

        $('#companyFilter').on('change', function () {
            table.ajax.reload();
        });
        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>



 @endsection()
