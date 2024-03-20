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
<table id="dataTable1" class="" cellSpacing="0" width="100%">
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

    <script>
    $(document).ready(function() {
       let dataTable1 = $('#dataTable1').DataTable({
            "autoWidth": false,
            "lengthMenu": [10, 25, 50, 100,], // Set the available page lengths
            "pageLength": 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('datatable-failed.getFailedData') }}",
                data: function (d) {
                    d.dateFilter = $('#dateFilter').val();
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
            dom: 'Bfrtip',
            buttons: [
            { extend: 'copyHtml5', className: 'btn btn-outline-primary' },
            { extend: 'excelHtml5', className: 'btn btn-outline-success' },
            { extend: 'csvHtml5', className: 'btn btn-outline-info' },
            { extend: 'pdfHtml5', className: 'btn btn-outline-danger' }
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
            dataTable1.ajax.reload();
        });
    });



    </script>


 @endsection()
