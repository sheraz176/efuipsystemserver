@extends('superadmin.layout.master')
@include('superadmin.partials.style')

@section('content')
<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Hourly Transaction Summary</li>
                </ol>
            </nav>

            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('superadmin.hourly-summary') }}">
                                <label for="dateFilter">Filter by Date:</label>
                                <input type="text" id="dateFilter" name="dateFilter" class="form-control"
                                       placeholder="Select date range" value="{{ request('dateFilter') }}">
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

<script type="text/javascript">
$(function() {
    // Date range picker
    $('#dateFilter').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' to ',
            applyLabel: 'Apply',
            cancelLabel: 'Clear'
        }
    });

    $('#dateFilter').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
        table.ajax.reload();
    });

    $('#dateFilter').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        table.ajax.reload();
    });

    var table = $('#hourlySummaryTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('superadmin.hourly-summary') }}",
            type: 'GET',
            data: function(d) {
                d.dateFilter = $('#dateFilter').val();
            }
        },
        columns: [
            { data: 'hour', name: 'hour' },
            { data: 'call_center_count', name: 'call_center_count' },
            { data: 'call_center_amount', name: 'call_center_amount' },
            { data: 'ivr_count', name: 'ivr_count' },
            { data: 'ivr_amount', name: 'ivr_amount' },
            { data: 'merchant_count', name: 'merchant_count' },
            { data: 'merchant_amount', name: 'merchant_amount' },
            { data: 'app_count', name: 'app_count' },
            { data: 'app_amount', name: 'app_amount' },
            { data: 'recursive_count', name: 'recursive_count' },
            { data: 'recursive_amount', name: 'recursive_amount' },
            { data: 'summary_date', name: 'summary_date' }
        ]
    });
});
</script>

@include('superadmin.partials.script')
@endsection
