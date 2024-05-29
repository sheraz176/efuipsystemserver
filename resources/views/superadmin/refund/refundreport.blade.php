@extends('superadmin.layout.master')

@section('content')
    <div>
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="POST" action="{{ route('superadmin.RefundedDataExport') }}">
                    @csrf
                    <label for="dateFilter">Filter by Date:</label>
                    <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
            </div>
            <div class="col-md-4 mt-4" style="marign-top:10%;">
                <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
            </div>
            </form>
            {{-- <div class="col-md-4">
                <label for="msisdn">Search by Mobile Number:</label>
                <input type="text" id="msisdn" class="form-control" placeholder="Enter Customer MSISDN">
            </div> --}}
        </div>

        <table id="myTables" class="display myTables" cellSpacing="0" width="100%">
            <thead>
                <tr>
                    <th>Refunded ID</th>
                    <th>Customer MSISDN</th>
                    <th>Transaction ID</th>
                    <th>Reference ID</th>
                    <th>Amount</th>
                    <th>Refunded By</th>
                    <th>Plan Name</th>
                    <th>Product Name</th>
                    <th>Company Name</th>
                    <th>Medium</th>
                    <th>Subscription Date</th>
                    <th>Unsubscription Date</th>
                </tr>
            </thead>
        </table>
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

            var table = $('#myTables').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('manage-refunds.getRefundedData') }}",
                    data: function (d) {
                        var dateFilter = $('#dateFilter').val();
                        if (dateFilter) {
                            d.dateFilter = dateFilter;
                        }
                    }
                },
                columns: [
                    {data: 'refund_id', name: 'refund_id'},
                    {data: 'subscriber_msisdn', name: 'subscriber_msisdn'},
                    { data: 'transaction_id', name: 'transaction_id' },
                    { data: 'reference_id', name: 'reference_id' },
                    { data: 'transaction_amount', name: 'transaction_amount' },
                    { data: 'refunded_by', name: 'refunded_by' },
                    { data: 'plan_name', name: 'plan_name' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'company_name', name: 'company_name' },
                     { data: 'medium', name: 'medium' },
                     { data: 'subscription_time', name: 'subscription_time' },
                     { data: 'unsubscription_datetime', name: 'unsubscription_datetime' },
                ]
            });

            var search_input = document.querySelectorAll('.dataTables_filter input');
            search_input.forEach(Element => {
                Element.placeholder = 'Search by name';
            });
        });
    </script>
@endsection
