@extends('superadmin.layout.master')
@include('superadmin.partials.style')

@section('content')

<div class="ms-content-wrapper">
    <div class="row">




        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Recursive Charging Counts Report</li>
                </ol>
            </nav>
        </div>

        <div class="col-xl-12 col-md-12">
            <div class="ms-card">
                <div class="ms-card-body">

<div class="row mb-3 justify-content-center align-items-center">
    <div class="col-md-4">
        <input type="text"
               id="dateFilter"
               class="form-control"
               placeholder="Select date range">
    </div>

    <div class="col-md-2 text-center">
        <button id="exportCsv" class="btn btn-danger">
            Export CSV
        </button>
    </div>
</div>


                    <h4 class="text-center mb-3">Recursive Charging Summary (Hourly Update)</h4>

                    <table id="recusiveCountsTable" class="display table table-striped table-bordered" style="width:100%">
                        <thead style="background: #f2f2f2;">
                            <tr>
                                   <th>#</th>
                                <th>Date</th>
                                <th>Today Recursive</th>
                                <th>Success Total</th>
                                <th>Failed Total</th>

                                <th>Term Life Daily Success</th>
                                <th>Term Life Monthly Success</th>
                                <th>Family Health Daily Success</th>
                                <th>Family Health Monthly Success</th>

                             
                               
                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>

    </div>
</div>

{{-- ================= CDN FIXED ORDER ================= --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<link href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
{{-- =================================================== --}}


<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(function () {

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

    $('#dateFilter').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(
            picker.startDate.format('YYYY-MM-DD') +
            ' to ' +
            picker.endDate.format('YYYY-MM-DD')
        );
        table.ajax.reload();
    });

    $('#dateFilter').on('cancel.daterangepicker', function () {
        $(this).val('');
        table.ajax.reload();
    });
    var table = $('#recusiveCountsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,

        ajax: {
            url: "{{ route('superadmin.recusive.counts.getdata') }}",
            data: function (d) {
                let dateFilter = $('#dateFilter').val();
                if (dateFilter) {
                    d.dateFilter = dateFilter;
                }
            }
        },

        columns: [
            { data: 'DT_RowIndex', orderable: false },
            { data: 'date' },
            { data: 'total_recursive_today' },
            { data: 'success_total' },
            { data: 'failed_total' },
            { data: 'term_life_daily_count' },
            { data: 'term_life_monthly_count' },
            { data: 'family_health_daily_count' },
            { data: 'family_health_monthly_count' },
                ]
    });

});
</script>
<script>
$('#exportCsv').on('click', function () {

    let dateFilter = $('#dateFilter').val();
    let url = "{{ route('superadmin.recusive.counts.getdata') }}?export=csv";

    if (dateFilter) {
        url += '&dateFilter=' + encodeURIComponent(dateFilter);
    }

    window.location.href = url;
});
</script>


@include('superadmin.partials.script')
@endsection
