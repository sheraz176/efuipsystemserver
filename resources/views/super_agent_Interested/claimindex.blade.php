@extends('super_agent_Interested.layout.master')
@include('superadmin.partials.style')


@section('content')
    <div class="container mt-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="#"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"> Claims Report</li>
                </ol>
            </nav>
            <div class="ms-panel">
    <div class="ms-panel-header ms-panel-custome align-items-center">
        <form method="POST" action="{{ route('superadmin.export-claim-data') }}">
            @csrf
            <div class="row">

                {{-- Date Filter --}}
                <div class="col-md-6">
                    <label for="dateFilter">Filter by Date:</label>
                    <input type="text" id="dateFilter" name="dateFilter" class="form-control" placeholder="Select date range">
                </div>

                {{-- Status Filter --}}
                <div class="col-md-3">
                    <label for="statusFilter">Status:</label>
                    <select id="statusFilter" class="form-control">
                        <option value="">All</option>
                        <option value="In Process">In Process</option>
                        <option value="Approved">Approved</option>
                        <option value="Reject">Reject</option>
                    </select>
                </div>

                {{-- Type Filter --}}
                {{-- <div class="col-md-3">
                    <label for="typeFilter">Filter by Type:</label>
                    <input type="text" id="typeFilter" class="form-control" placeholder="Enter type">
                </div> --}}

                {{-- Export Button --}}
                <div class="col-md-3 mt-4">
                    <button type="submit" class="btn btn-primary btn-sm mt-2">
                        <i class='bx bx-down-arrow-alt'></i> Export
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

        </div>

        <div class="card">
            <div class="card-body">
                <table id="myTables" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>MSISDN</th>
                            <th>Plan ID</th>
                            <th>Product ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>History Name</th>
                            <th>Doctor Prescription</th>
                            <th>Medical Bill</th>
                            <th>Lab Bill</th>
                            <th>Other</th>
                            <th>Existing Amount</th>
                            <th>Remaining Amount</th>
                            <th>Amount</th>
                            <th>Claim Amount</th>
                            <th>Update Claim Amount</th>
                            <th>Status Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!-- Edit Amount Modal -->
    <div class="modal fade" id="editAmountModal" tabindex="-1" aria-labelledby="editAmountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="updateAmountForm">
                @csrf
                <input type="hidden" name="claim_id" id="claim_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Amount</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="new_amount">Claim Amount</label>
                        <input type="number" name="new_amount" id="new_amount" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>




    <script>
    $(function() {
        // Date Picker Init
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

        $('#dateFilter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
            table.ajax.reload();
        });

        $('#dateFilter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            table.ajax.reload();
        });

        var table = $('#myTables').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('superadmin.get-claims-data') }}",
                data: function(d) {
                    d.dateFilter = $('#dateFilter').val();
                    d.status = $('#statusFilter').val();
                    d.type = $('#typeFilter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'msisdn', name: 'msisdn' },
                { data: 'plan_name', name: 'plan_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'status', name: 'status' },
                { data: 'date', name: 'date' },
                { data: 'type', name: 'type' },
                { data: 'history_name', name: 'history_name' },
                { data: 'doctor_prescription', name: 'doctor_prescription', orderable: false, searchable: false },
                { data: 'medical_bill', name: 'medical_bill', orderable: false, searchable: false },
                { data: 'lab_bill', name: 'lab_bill', orderable: false, searchable: false },
                { data: 'other', name: 'other' },
                { data: 'existingamount', name: 'existingamount' },
                { data: 'remaining_amount', name: 'remaining_amount' },
                { data: 'amount', name: 'amount' },
                { data: 'claim_amount', name: 'claim_amount' },
                { data: 'edit_amount', name: 'edit_amount', orderable: false, searchable: false },
                { data: 'status_action', name: 'status_action', orderable: false, searchable: false },
            ]
        });

        // Trigger reload on status/type change
        $('#statusFilter, #typeFilter').on('change', function() {
            table.ajax.reload();
        });

        // Approve/Reject
        $(document).on('click', '.approve-btn, .reject-btn', function() {
            let id = $(this).data('id');
            let status = $(this).hasClass('approve-btn') ? 'Approved' : 'Reject';

            if (confirm(`Are you sure to mark as ${status}?`)) {
                $.ajax({
                    url: '{{ route('claim.update.status') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        status: status
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        table.ajax.reload();
                        loadClaimStatusCounts(); // Optional: Update counters
                    }
                });
            }
        });

        // Edit Claim Amount Modal
        $(document).on('click', '.edit-amount-btn', function() {
            $('#claim_id').val($(this).data('id'));
            $('#new_amount').val($(this).data('amount'));
            $('#editAmountModal').modal('show');
        });

        $('#updateAmountForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route('claim.update.amount') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    $('#editAmountModal').modal('hide');
                    table.ajax.reload(null, false);
                },
                error: function() {
                    toastr.error('Failed to update amount.');
                }
            });
        });

        // Placeholder for search box
        $('.dataTables_filter input').attr('placeholder', 'Search by name');
    });
</script>


    @include('superadmin.partials.script')
@endsection
