@extends('agent.layout.master')
@include('agent.partials.style')
<link href="{{ asset('newdes/assets/css/toastr.min.css') }}" rel="stylesheet">
@section('content')
    <div class="ms-content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb pl-0">
                        <li class="breadcrumb-item"><a href="#"><i
                                    class="material-icons"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bulk Refunds Manager</li>
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
                            <a href="{{ route('agent.builkmanager.logsindex') }}"> <button type="submit"
                                    class="btn btn-primary btn-sm">Api Logs</button>
                            </a>
                            <a href="{{ route('agent.builkmanager.create') }}"> <button type="submit"
                                    class="btn btn-danger btn-sm"><i class='bx bx-down-arrow-alt'></i>Upload File</button>
                            </a>
                            <a href="{{ route('agent.download.sample.csv') }}" class="btn btn-primary btn-sm">
                                Download Sample CSV
                            </a>
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
                                    <th>ID</th>
                                    <th>Subscription ID</th>
                                    <th>Msisdn</th>
                                    <th>Reason</th>

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
                    url: "{{ route('agent.builkmanager.getData') }}",
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
                        data: 'subsecribe_id',
                        name: 'subsecribe_id'
                    },
                    {
                        data: 'msisdn',
                        name: 'msisdn'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },

                ]
            });

            var search_input = document.querySelectorAll('.dataTables_filter input');
            search_input.forEach(Element => {
                Element.placeholder = 'Search by name';
            });
        });
    </script>
    @include('agent.partials.script')
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

    <script src="{{ asset('newdes/assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('newdes/assets/js/toast.js') }}"></script>




@endsection
