@extends('superadmin.layout.master')

<head>

    <link href="{{asset('newdes/assets/css/style.css')}}" rel="stylesheet">

<style>
/* styles.css */

.daterangepicker {
    z-index: 1000; /* Ensure this value is higher than other elements */
    position: relative; /* Adjust positioning if necessary */
}

</style>
</head>

@section('content')


<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons">home</i> Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Add Doctor</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Doctor List</li>
                </ol>
            </nav>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="POST" action="{{ route('superadmin.export-recusive-charging-data') }}">
                                @csrf
                            <label for="dateFilter">Filter by Date:</label>
                            <input type="text" id="dateFilter" name="dateFilter" class="form-control daterangepicker" placeholder="Select date range">
                        </div>
                        <div class="col-md-4 mt-6" style="marign-top:-12%;">
                              <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
                        </div>
                           </form>
                    </div>

                    <div class="date-range-filter">
                        <label for="start-date">Start Date:</label>
                        <input type="date" id="start-date" name="start-date">

                        <label for="end-date">End Date:</label>
                        <input type="date" id="end-date" name="end-date">

                        <button id="filter-button">Filter</button>
                    </div>


                    <button class="btn btn-primary d-inline w-20" type="submit">Add Doctor</button>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12">
            <div class="ms-card">
                <div class="ms-card-body">

                    <table id="myTables" class="display myTables" style="width:100%">
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
