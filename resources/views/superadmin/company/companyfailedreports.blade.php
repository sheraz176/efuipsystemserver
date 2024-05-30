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
<table id="myTables" class="display myTables" cellSpacing="0" width="100%">
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
                url: "{{ route('companies-reports.companies-failed-data') }}",
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



 @endsection()
