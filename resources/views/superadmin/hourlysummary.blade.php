@extends('superadmin.layout.master')
@include('superadmin.partials.style')

@section('content')
<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('superadmin.dashboard') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">Hourly Transaction Summary</li>
                </ol>
            </nav>

            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('superadmin.hourly-summary') }}">
                                <label>Filter by Date:</label>
                                <input type="text" id="dateFilter" name="dateFilter" class="form-control"
                                       value="{{ request('dateFilter') }}">
                        </div>
                        <div class="col-md-6 mt-4">
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            <button type="submit" name="export" value="1" class="btn btn-success btn-sm">Export CSV</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="ms-panel-body">
                    <table id="hourlySummaryTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Hour</th>
                                <th>Call Center Count</th>
                                <th>Call Center Amount</th>
                                <th>IVR Count</th>
                                <th>IVR Amount</th>
                                <th>Merchant Count</th>
                                <th>Merchant Amount</th>
                                <th>App Count</th>
                                <th>App Amount</th>
                                <th>Recursive Count</th>
                                <th>Recursive Amount</th>
                                <th>Summary Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(function() {

    var today = moment().format('YYYY-MM-DD');

    $('#dateFilter').daterangepicker({
        opens: 'left',
        autoUpdateInput: true,
        startDate: moment(),
        endDate: moment(),
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' to '
        }
    });

    // Default aaj ki date agar empty ho
    if (!$('#dateFilter').val()) {
        $('#dateFilter').val(today + ' to ' + today);
    }

    var table = $('#hourlySummaryTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 30,
        lengthMenu: [[30, 50, 100, -1], [30, 50, 100, "All"]],

        ajax: {
            url: "{{ route('superadmin.hourly-summary') }}",
            type: 'GET',
            data: function(d) {
                d.dateFilter = $('#dateFilter').val() || (today + ' to ' + today);
            }
        },

        columns: [
            { data: 'hour', name: 'hour' },
            { data: 'call_center_count', name: 'call_center_count' },
            { data: 'call_center_amount', name: 'call_center_amount',
                render: data => parseFloat(data || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) },
            { data: 'ivr_count', name: 'ivr_count' },
            { data: 'ivr_amount', name: 'ivr_amount',
                render: data => parseFloat(data || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) },
            { data: 'merchant_count', name: 'merchant_count' },
            { data: 'merchant_amount', name: 'merchant_amount',
                render: data => parseFloat(data || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) },
            { data: 'app_count', name: 'app_count' },
            { data: 'app_amount', name: 'app_amount',
                render: data => parseFloat(data || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) },
            { data: 'recursive_count', name: 'recursive_count' },
            { data: 'recursive_amount', name: 'recursive_amount',
                render: data => parseFloat(data || 0).toLocaleString('en-US', { minimumFractionDigits: 2 }) },
            { data: 'summary_date', name: 'summary_date' }
        ]
    });

    $('#dateFilter').on('apply.daterangepicker', function() {
        table.ajax.reload();
    });

});
</script>

@include('superadmin.partials.script')
@endsection
