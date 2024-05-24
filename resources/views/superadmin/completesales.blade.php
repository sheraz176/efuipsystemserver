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

<table  id="myTables" class="display myTables" cellSpacing="0" width="100%">
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
                <th>Consent</th>
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
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5'
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatable.getData') }}",
                    data: function (d) {
                        var dateFilter = $('#dateFilter').val();
                        if (dateFilter) {
                            d.dateFilter = dateFilter;
                        }
                    }
                },
                columns: [
                    {data: 'subscription_id', name: 'subscription_id'},
                    {data: 'subscriber_msisdn', name: 'subscriber_msisdn'},
                    {data: 'plan_name', name: 'plan_name'},
                    {data: 'product_name', name: 'product_name'},
                    {data: 'transaction_amount', name: 'transaction_amount'},
                    {data: 'product_duration', name: 'product_duration'},
                    {data: 'company_name', name: 'company_name'},
                    {data: 'sales_agent', name: 'sales_agent'},
                    {data: 'cps_transaction_id', name: 'cps_transaction_id'},
                    {data: 'referenceId', name: 'product_duration'},
                    {data: 'recursive_charging_date', name: 'recursive_charging_date'},
                    {data: 'subscription_time', name: 'subscription_time'},
                    {data: 'grace_period_time', name: 'grace_period_time'},
                    {data: 'consistent_provider', name: 'consistent_provider'},
                ]
            });

            var excel_btn = document.querySelector('.buttons-excel span');
            excel_btn.innerHTML = `Export <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 16L4 17C4 18.6569 5.34315 20 7 20L17 20C18.6569 20 20 18.6569 20 17L20 16M16 8L12 4M12 4L8 8M12 4L12 16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`;

            var search_input = document.querySelectorAll('.dataTables_filter input');
            search_input.forEach(Element => {
                Element.placeholder = 'Search by name';
            });
        });
    </script>









 @endsection
