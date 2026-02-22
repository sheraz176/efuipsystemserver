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
            <div class="col-md-4">
                <label for="msisdn">Search by Mobile Number:</label>
                <input type="text" id="msisdn" class="form-control" placeholder="Enter Customer MSISDN">
            </div>
        </div>

        <table id="refunded_table" class="" cellSpacing="0" width="100%">
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
    $(document).ready(function() {
        let dataTable = $('#refunded_table').DataTable({
            "autoWidth": false,
            "lengthMenu": [10, 25, 50, 100, -1],
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('manage-refunds.getRefundedData') }}",
                data: function(d) {
                    d.dateFilter = $('#dateFilter').val();
                    d.msisdn = $('#msisdnFilter').val(); // Assuming you have an input field with id msisdnFilter
                }
            },
            columns: [
                { data: 'refund_id', name: 'refund_id' },
                { data: 'subscriber_msisdn', name: 'subscriber_msisdn' },
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
            ],
            "columnDefs": [
                { "searchable": false, "targets": [0, 1, 2, 3, 4, 5, 6, 7, 9] }
            ],
            "initComplete": function(settings, json) {
                let searchInput = $('#refunded_table_filter input');
                let debounceTimeout;
                searchInput.off('input').on('input', function() {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => {
                        dataTable.search(this.value).draw();
                    }, 500);
                });
            }
        });

        $('#dateFilter').daterangepicker({
            opens: 'left',
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

        $('#dateFilter, #msisdnFilter').on('change', function() {
            dataTable.ajax.reload();
        });
    });
</script>
@endsection
