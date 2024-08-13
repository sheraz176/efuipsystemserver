@extends('superadmin.layout.master')
@include('superadmin.partials.style')
@section('content')



<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Telesales Basic Agent</li>
                </ol>
            </nav>
            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <h6>Telesales Basic Agent  </h6>
                    <a href="{{ route('telesales-agents.create') }}" data-toggle="modal"
                        class="btn btn-primary d-inline w-20" type="submit">Create New Basic Telesales Agent</a>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-md-12">
            <div class="ms-card">
                <div class="ms-card-body">

                    <table id="myTables" class="display myTables" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Current Status</th>
                                <th>Username</th>
                                <th>Login Status</th>
                                <th>Call Status</th>
                                <th>Login Time Today</th>
                                <th>Logout Time Today</th>
                                <th>Email</th>
                                <th>Company ID</th>
                                <th>Emp Code</th>
                                <th>Actions</th>

                            </tr>
                        </thead>

                    </table>
                </div>
            </div>

        </div>
    </div>
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
                url: "{{ route('superadmin.basic.agent.data') }}",
                data: function (d) {
                    var dateFilter = $('#dateFilter').val();
                    if (dateFilter) {
                        d.dateFilter = dateFilter;
                    }
                }
            },
            columns: [
                { data: 'agent_id', name: 'agent_id' },
                { data: 'first_name', name: 'first_name' },
                { data: 'status', name: 'status' },
                { data: 'username', name: 'username' },
                { data: 'islogin', name: 'islogin' },
                { data: 'call_status', name: 'call_status' },
                { data: 'today_login_time', name: 'today_login_time' },
                { data: 'today_logout_time', name: 'today_logout_time' },
                { data: 'email', name: 'email' },
                { data: 'company_id', name: 'company_id' },
                { data: 'emp_code', name: 'emp_code' },
                { data: 'action', name: 'action' },
            ],
        });



        var search_input = document.querySelectorAll('.dataTables_filter input');
        search_input.forEach(Element => {
            Element.placeholder = 'Search by name';
        });
    });
</script>


@include('superadmin.partials.script');

@endsection


