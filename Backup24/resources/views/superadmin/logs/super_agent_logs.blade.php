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
                        <li class="breadcrumb-item active" aria-current="page">Auto Debit Api Logs</li>
                    </ol>
                </nav>

                <div class="ms-panel">
                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <div class="row mb-3">
                            {{-- <label for="dateFilter">upload</label> --}}

                        </div>
                        <div class="col-md-6">

                            {{-- <label for="dateFilter">Filter by Date:</label> --}}

                        </div>

                        <div class="col-md-4 mt-6" style="margin-left: -40%">


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
                                    <th>Logs ID</th>
                                    <th>Msisdn</th>
                                    <th>Result Code</th>
                                    <th>Result Desc</th>
                                    <th>Transaction ID</th>
                                    <th>Reference ID</th>
                                    <th>Failed Response</th>
                                    <th>Date & Time</th>
                                    <th>Api Url</th>

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
            $('#dateFilter').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format(
                    'YYYY-MM-DD'));
                table.ajax.reload();
            });

            // Clear the input field when date range is canceled
            $('#dateFilter').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            var table = $('#myTables').DataTable({
                responsive: true,

                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('superadmin.auto.debit.api.log.data') }}",
                    data: function(d) {
                        var dateFilter = $('#dateFilter').val();
                        if (dateFilter) {
                            d.dateFilter = dateFilter;
                        }
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'msisdn',
                        name: 'msisdn'
                    },
                    {
                        data: 'resultCode',
                        name: 'resultCode'
                    },
                    {
                        data: 'resultDesc',
                        name: 'resultDesc'
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id'
                    },
                    {
                        data: 'reference_id',
                        name: 'reference_id'
                    },
                    {
                        data: 'cps_response',
                        name: 'cps_response'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'api_url',
                        name: 'api_url'
                    },




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
