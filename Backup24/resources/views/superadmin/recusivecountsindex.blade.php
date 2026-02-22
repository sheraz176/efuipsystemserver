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

                    <h4 class="text-center mb-3">Recursive Charging Summary (Hourly Update)</h4>

                    <table id="recusiveCountsTable" class="display table table-striped table-bordered" style="width:100%">
                        <thead style="background: #f2f2f2;">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Total Recursive Today</th>
                                <th>Success Total</th>
                                <th>Family Health Success (Plan 4)</th>
                                <th>Term Life Success (Plan 1)</th>
                                <th>Failed Total</th>
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

    var table = $('#recusiveCountsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,

        ajax: {
            url: "{{ route('superadmin.recusive.counts.getdata') }}",
            type: "GET"
        },

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'date', name: 'date' },
            { data: 'total_recursive_today', name: 'total_recursive_today' },
            { data: 'success_total', name: 'success_total' },
            { data: 'success_family_health', name: 'success_family_health' },
            { data: 'success_term_life', name: 'success_term_life' },
            { data: 'failed_total', name: 'failed_total' },
        ]
    });

    // Search box placeholder
    $('.dataTables_filter input').attr('placeholder', 'Search...');

});
</script>

@include('superadmin.partials.script')
@endsection
