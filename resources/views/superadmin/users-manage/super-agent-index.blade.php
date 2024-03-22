@extends('superadmin.layout.master')

@section('content')
<div class="">
    <h2>Telesales Agents</h2>
    @if(count($super_agents) > 0)


    <table id="agents" class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Current Status</th>
                <th>Username</th>
                <th>Login Status</th>
                <th>Call Status</th>
                <th style="width: 150px;">Login Time Today</th>
                <th style="width: 150px;">Logout Time Today</th>
                <th>Email</th>
                <th>Company ID</th>

            </tr>
        </thead>
        <tbody>


            @foreach($super_agents as $super_agents)
            <tr>
                <td>{{ $super_agents->super_agent_id }}</td>
                <td>{{ $super_agents->first_name }}</td>
                <td>
                    @if($super_agents->status == 1)
                        <button class="btn btn-success">Active</button>
                    @else
                        <button class="btn btn-danger">Inactive</button>
                    @endif
                </td>
                <td>{{ $super_agents->username }}</td>
                <td>
                    @if($super_agents->islogin == 1)
                        <button class="btn btn-success">Logged In</button>
                    @else
                        <button class="btn btn-danger">Logged Out</button>
                    @endif
                </td>
                <td>{{ $super_agents->call_status }}</td>
                <td>{{ $super_agents->today_login_time }}</td>
                <td>{{ $super_agents->today_logout_time }}</td>
                <td>{{ $super_agents->email }}</td>
                <td>{{ $super_agents->company_id }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No Super agents available.</p>
    @endif
</div>

<script>
let table = new DataTable('#agents');
</script>



@endsection
