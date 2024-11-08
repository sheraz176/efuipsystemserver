@extends('superadmin.layout.master')
@include('superadmin.partials.style')
@section('content')



<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Total Consent Number Check List</li>
                </ol>
            </nav>
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="fw-medium d-block mb-1">Today Consent Number Count</span>
                         <h3 class="card-title mb-2">{{ number_format($ConsentNumberDatacount, 0, '.', ',') }}</h3>
                    </div>
                </div>
            </div>
            <div class="ms-panel">
                <form method="POST" action="{{ route('superadmin.export-consent-number-data') }}">
                    @csrf
                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <div class="row mb-3">
                        </div>
                        <div class="col-md-2">


                        </div>
                        <div class="col-md-4">
                            <label for="causesFilter">Filter By Causes:</label>
                            <select id="causesFilter" class="form-select">
                                <option value="">All</option>
                                <option value="Success">Success</option>
                                <option value="Failed">Failed</option>


                            </select>
                        </div>
                        <div class="col-md-4">

                            <label for="dateFilter">Filter by Date:</label>
                            <input type="text" id="dateFilter" name="dateFilter" class="form-control "
                                placeholder="Select date range">
                        </div>

                        <div class="col-md-2 mt-8" style="margin-top: 2%">
                            <button type="submit" class="btn btn-primary btn-sm"><i
                                    class='bx bx-down-arrow-alt'></i>Export</button>

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
                                <th>ID</th>
                                <th>Msisdn</th>
                                <th>Agent ID</th>
                                <th>Company</th>
                                <th>Plan</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Response</th>
                                <th>Date</th>
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

        $('#causesFilter').on('change', function () {
            table.ajax.reload();
        });

        var table = $('#myTables').DataTable({
            responsive: true,

            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('superadmin.ConsentDataGet') }}",
                data: function (d) {
                    var dateFilter = $('#dateFilter').val();
                    if (dateFilter) {
                        d.dateFilter = dateFilter;
                    }
                    var causesFilter = $('#causesFilter').val();
                    if (causesFilter) {
                        d.causesFilter = causesFilter;
                    }
                }
            },
            columns: [
            { data: 'id', name: 'id' },
            { data: 'msisdn', name: 'msisdn' },
            { data: 'agent_id', name: 'agent_id' },
            { data: 'company_name', name: 'company_name' },
            { data: 'plan_name', name: 'plan_name' },
            { data: 'product_name', name: 'product_name' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: 'response', name: 'response' },
            { data: 'created_at', name: 'created_at' },


            ],
        });
        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>
@include('superadmin.partials.script')
 @endsection()
