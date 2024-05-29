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

<table id="myTables" class="display myTables" cellSpacing="0" width="100%">
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


<script type="text/javascript">
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
                url: "{{ route('superadmin.get-recusive-charging-data') }}",
                data: function (d) {
                    var dateFilter = $('#dateFilter').val();
                    if (dateFilter) {
                        d.dateFilter = dateFilter;
                    }
                    var companyFilter = $('#companyFilter').val();
                    if (companyFilter) {
                        d.companyFilter = companyFilter;
                    }
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

            ]
        });

        $('#companyFilter').on('change', function () {
            table.ajax.reload();
        });
        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>




 @endsection
