@extends('company_manager.layout.master')

@section('content')

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="companyFilter">Filter by Registered Agents:</label>
            <select id="companyFilter" class="form-select" style="max-height: 150px; overflow-y: auto;">
                <option value="">All Active/ Non Active Agents</option>

                @foreach($agents->take(10) as $agent)
                    <option value="{{ $agent->agent_id }}">{{ $agent->username }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <form method="POST" action="{{ route('company-manager.agents-sale-data-export') }}">
                @csrf
            <label for="dateFilter">Filter by Date:</label>
            <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
        </div>
        <div class="col-md-4 mt-4" style="marign-top:10%;">
            <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
            </form>
         </div>
    </div>

<table id="dataTable" class="" cellSpacing="0" width="100%">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Transaction ID</th>
                <th>Refernce ID</th>
                <th>Sale Request Time</th>
                <th>Customer Number</th>
                <th>Failed Message</th>
                <th>Failed Information</th>
                <th>Amount</th>
                <th>Product ID</th>
                <th>Plan ID</th>
                <th>Company</th>
            </tr>
        </thead>
    </table>
</div>

    <script>
    $(document).ready(function() {
       let dataTable= $('#dataTable').DataTable({
            "autoWidth": false,
            "columnDefs": [
                    { "width": "15%", "targets": 3 },
                    { "width": "10%", "targets": 5 },
                    { "width": "40%", "targets": 6 },
                    { "width": "15%", "targets": 8 },
                    { "width": "20%", "targets": 9 },
                ],
            "lengthMenu": [10, 25, 50, 100,-1], // Set the available page lengths
            "pageLength": 10,
            processing: true,
            serverSide: true,
             ajax: {
                url: "{{ route('company-manager.companies-reports.agents-sales-data') }}",
                data: function (d) {
                    d.companyFilter = $('#companyFilter').val();
                    d.dateFilter = $('#dateFilter').val();
                }
            },
            columns: [
            { data: 'request_id', name: 'insufficient_balance_customers.request_id' },
            { data: 'transactionId', name: 'insufficient_balance_customers.transactionId' },
            { data: 'referenceId', name: 'insufficient_balance_customers.referenceId' },
            { data: 'timeStamp', name: 'insufficient_balance_customers.timeStamp' },
            { data: 'accountNumber', name: 'insufficient_balance_customers.accountNumber' },
            { data: 'resultDesc', name: 'insufficient_balance_customers.resultDesc' },
            { data: 'failedReason', name: 'insufficient_balance_customers.failedReason' },
            { data: 'amount', name: 'insufficient_balance_customers.amount' },
            { data: 'plan_name', name: 'plans.plan_name' },
            { data: 'product_name', name: 'products.product_name' },
            { data: 'company_name', name: 'company_profiles.company_name' },


            ],

        });

        // Initialize datepicker
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
        $('#companyFilter, #dateFilter').on('change', function () {
            dataTable.ajax.reload();
        });
    });



    </script>


 @endsection()