@extends('superadmin.layout.master')

@section('content')
<div class="">
    <h2>Companies Managers</h2>
    @if(count($companies_managers) > 0)


    <table id="agents" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Cnic</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Company ID</th>
                <th>Ip Address</th>
            </tr>
        </thead>
        <tbody>


            @foreach($companies_managers as $companies_managers)
            <tr>
                <td>{{ $companies_managers->id }}</td>
                <td>{{ $companies_managers->first_name }}</td>
                <td>{{ $companies_managers->last_name }}</td>
                <td>{{ $companies_managers->username }}</td>
                <td>{{ $companies_managers->cnic }}</td>
                <td>{{ $companies_managers->phone_number }}</td>
                <td>{{ $companies_managers->email }}</td>
                <td>{{ $companies_managers->company_id }}</td>
                <td>{{ $companies_managers->ip_address }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No companies managers available.</p>
    @endif
</div>

<script>
let table = new DataTable('#agents');
</script>



@endsection
