@extends('superadmin.layout.master')

@section('content')

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="POST" action="{{ route('superadmin.export-complete.sale') }}">
                @csrf
            <label for="dateFilter">Filter by Date :</label>
            <input type="text" id="dateFilter" name="dateFilter"  class="form-control" placeholder="Select date range" required/>
        </div>
        <div class="col-md-4 mt-4" style="marign-top:10%;">
              <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
        </div>
             </form>
    </div>
</div>

<table id="dataTable2" class="" cellSpacing="0" width="100%">
        <thead>
            <tr>
                <th>Subscription ID</th>
                <th>Customer MSISDN</th>
                <th>Plan Name</th>
                <th>Product Name</th>
                <th>Amount</th>
                <th>Duration</th>
                <th>Company Name</th>
                <th>Agent Name</th>
                <th>Transaction ID</th>
                <th>Reference ID</th>
                <th>Next Charging Date</th>
                <th>Subscription Date</th>
                <th>Free Look Period</th>
            </tr>
        </thead>
    </table>




<script>
    $(document).ready(function() {
       let dataTable2 = $('#dataTable2').DataTable({
            "autoWidth": false,
            "lengthMenu": [10, 25, 50, 100,], // Set the available page lengths
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatable.getData') }}",
                data: function (d) {
                    d.dateFilter = $('#dateFilter').val();
                }
            },
            columns: [
                 { data: 'subscription_id', name: 'subscription_id' },
                { data: 'subscriber_msisdn', name: 'subscriber_msisdn' },
                { data: 'plan_name', name: 'plan_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'transaction_amount', name: 'transaction_amount' },
                { data: 'product_duration', name: 'product_duration' },
                { data: 'company_name', name: 'company_name' },
                { data: 'sales_agent', name: 'sales_agent' },
                { data: 'cps_transaction_id', name: 'cps_transaction_id' },
                { data: 'referenceId', name: 'product_duration' },
                { data: 'recursive_charging_date', name: 'recursive_charging_date' },
                { data: 'subscription_time', name: 'subscription_time' },
                { data: 'grace_period_time', name: 'grace_period_time' },
            ],
            "columnDefs": [
            { "searchable": false, "targets": [0,2,3,4,5,6,7,9,10,11,12] } // Disable search for columns 2 and 3 (plan_name and product_name)
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
            dataTable2.ajax.reload();
        });
    });

    </script>





 @endsection
