@extends('superadmin.layout.master')

@section('content')

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="POST" action="{{ route('superadmin.export-recusive-charging-data') }}">
                @csrf
            <label for="dateFilter">Filter by Date:</label>
            <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
        </div>
        <div class="col-md-4 mt-4" style="marign-top:10%;">
              <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
        </div>
           </form>
    </div>
</div>

<table id="dataTable" class="" cellSpacing="0" width="100%">
        <thead>
            <tr>
                <th>Subscription ID</th>
                 <th>Customer MSISDN</th>
                <th>Plan Name</th>
                <th>Product Name</th>
                <th>Transaction ID</th>
                <th>Reference ID</th>
                <th>Amount</th>
                <th>Cps Response</th>
                <th>Next Charging Date</th>
                 <th>Duration</th>


            </tr>
        </thead>
    </table>

    <script>
        $(document).ready(function() {
            let dataTable = $('#dataTable').DataTable({
                "autoWidth": false,

                "lengthMenu": [10, 25, 50, 100,-1], // Set the available page lengths
                "pageLength": 10, // Set the default page length
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('superadmin.get-recusive-charging-data') }}",
                    data: function (d) {
                        d.dateFilter = $('#dateFilter').val();
                    }
                },
                columns: [
                    { data: 'subscription_id', name: 'subscription_id' },
                     { data: 'customer_msisdn', name: 'customer_msisdn' },
                     { data: 'plan_name', name: 'plan_name' },
                     { data: 'product_name', name: 'product_name' },
                      { data: 'tid', name: 'tid' },
                     { data: 'reference_id', name: 'reference_id' },
                     { data: 'amount', name: 'amount'},
                     { data: 'cps_response', name: 'cps_response' },
                     { data: 'charging_date', name: 'charging_date' },
                     { data: 'duration', name: 'duration' },
                ],
                "columnDefs": [
            { "searchable": false, "targets": [0,1,2,3,4,5,6,7,9] } // Disable search for columns 2 and 3 (plan_name and product_name)
          ]
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

            // Apply the filters on change
            $('#dateFilter').on('change', function () {
                dataTable.ajax.reload();
            });
        });
    </script>






 @endsection
