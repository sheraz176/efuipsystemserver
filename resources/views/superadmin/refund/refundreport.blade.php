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
                <th>Unsubscription Date</th>
            </tr>
        </thead>
    </table>
</div>

    <script>
    $(document).ready(function() {
       let dataTable= $('#refunded_table').DataTable({
            "autoWidth": false,
            "searching": false,
            "columnDefs": [
                    { "width": "1%", "targets": 0 },
                    { "width": "10%", "targets": 1 },
                    { "width": "10%", "targets": 2 },
                    { "width": "10%", "targets": 3 },
                    { "width": "10%", "targets": 4 },
                    { "width": "15%", "targets": 6 },
                    { "width": "15%", "targets": 7 },
                    { "width": "15%", "targets": 10 },
                ],
            "lengthMenu": [10, 25, 50, 100,-1], // Set the available page lengths
            "pageLength": 10, // Set the default page length
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('manage-refunds.getRefundedData') }}",
                data: function (d) {
                    d.dateFilter = $('#dateFilter').val();
                    d.msisdn = $('#msisdn').val();
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
                { data: 'unsubscription_datetime', name: 'unsubscription_datetime' },

            ],
            "columnDefs": [
            { "searchable": false, "targets": [0,2,3,4,5,6,7,9,10] } // Disable search for columns 2 and 3 (plan_name and product_name)
          ]
        });

          $('#dateFilter').daterangepicker({
            opens: 'left', // Adjust the placement as needed
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

        // Apply the filters on change
        $('#dateFilter, #msisdn').on('change', function () {
            dataTable.ajax.reload();
        });

        // Initialize datepicker

        // Apply the filters on change
    });



    </script>


 @endsection()
