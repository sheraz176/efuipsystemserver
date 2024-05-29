@extends('superadmin.layout.master')

@section('content')

<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <form method="POST" action="{{ route('superadmin.export.failed-data') }}">
                @csrf
            <label for="dateFilter">Filter by Date:</label>
            <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
        </div>
        <div class="col-md-4 mt-4" style="marign-top:10%;">

              <button type="submit" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Export</button>
                </form>
        </div>
    </div>
</div>

<div>
<table id="myTables" class="display myTables" cellSpacing="0" width="100%">
        <thead>
            <tr>
                <th>Request ID</th>
                <th>Transaction ID</th>
                <th>MSISDN</th>
                <th>Request Time</th>
                <th>Plan Name</th>
                <th>Product Name</th>
                <th>Amount</th>
                <th>Refernce ID</th>
                <th>Result Code</th>
                <th>Result Summary</th>
                <th>Company Name</th>
                <th>Agent Name</th>
                <th>Source</th>
            </tr>
        </thead>
    </table>
</div>


<script >
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
                url: "{{ route('datatable-failed.getFailedData') }}",
                data: function (d) {
                    var dateFilter = $('#dateFilter').val();
                    if (dateFilter) {
                        d.dateFilter = dateFilter;
                    }
                }
            },
            columns: [
                { data: 'request_id', name: 'request_id' },
                { data: 'transactionId', name: 'transactionId' },
                { data: 'accountNumber', name: 'accountNumber' },
                { data: 'timeStamp', name: 'timeStamp' },
                { data: 'plan_name', name: 'plan_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'amount', name: 'amount' },
                { data: 'referenceId', name: 'referenceId' },
                { data: 'resultDesc', name: 'resultDesc' },
                { data: 'failedReason', name: 'failedReason' },
                { data: 'company_name', name: 'company_name' },
                { data: 'username', name: 'username' },
                { data: 'source', name: 'source' },
            ],
        });



        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>


 @endsection()
