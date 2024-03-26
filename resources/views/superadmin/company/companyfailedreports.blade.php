@extends('superadmin.layout.master')

@section('content')

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="companyFilter">Filter by Company:</label>
            <select id="companyFilter" class="form-select">
                <option value="">All Companies</option>

                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <form method="POST" action="{{ route('superadmin.companies-failed-data-export') }}">
                @csrf
            <label for="dateFilter">Filter by Date:</label>
            <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
    </div>
    <div class="col-md-4 mt-4" style="marign-top:10%;">
        <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
    </div>
  </form>
</div>
<table id="dataTable" class="" cellSpacing="0" width="100%">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Transaction ID</th>
                <th>Plan Name</th>
                <th>Product Name</th>
                <th>Refernce ID</th>
                <th>Sale Request Time</th>
                <th>Customer Number</th>
                <th>Failed Message</th>
                <th>Failed Information</th>
                <th>Amount</th>
                <th>Company</th>
            </tr>
        </thead>
    </table>
</div>

    <script>
    $(document).ready(function() {
       let dataTable= $('#dataTable').DataTable({
            "autoWidth": false,
            processing: true,
            serverSide: true,
             ajax: {
                url: "{{ route('companies-reports.companies-failed-data') }}",
                data: function (d) {
                    d.companyFilter = $('#companyFilter').val();
                    d.dateFilter = $('#dateFilter').val();
                }
            },
            columns: [
            { data: 'request_id', name: 'insufficient_balance_customers.request_id' },
            { data: 'transactionId', name: 'insufficient_balance_customers.transactionId' },
            { data: 'plan_name', name: 'plans.plan_name' },
            { data: 'product_name', name: 'products.product_name' },
            { data: 'referenceId', name: 'insufficient_balance_customers.referenceId' },
            { data: 'timeStamp', name: 'insufficient_balance_customers.timeStamp' },
            { data: 'accountNumber', name: 'insufficient_balance_customers.accountNumber' },
            { data: 'resultDesc', name: 'insufficient_balance_customers.resultDesc' },
            { data: 'failedReason', name: 'insufficient_balance_customers.failedReason' },
            { data: 'amount', name: 'insufficient_balance_customers.amount' },
            { data: 'company_name', name: 'company_profiles.company_name' },
            ],
            "columnDefs": [
            { "searchable": false, "targets": [0,2,3,4,5,6,7,9,10] } // Disable search for columns 2 and 3 (plan_name and product_name)
          ]

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
