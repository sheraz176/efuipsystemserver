@extends('superadmin.layout.master')

@section('content')
<div class="">
    <h2>Telesales Agents</h2>
    <a href="{{ route('telesales-agents.create') }}" class="btn btn-primary mb-3">Create New Telesales Agent</a>
    @if(count($telesalesAgents) > 0)

    
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>


            @foreach($telesalesAgents as $telesalesAgent)
            <tr>
                <td>{{ $telesalesAgent->agent_id }}</td>
                <td>{{ $telesalesAgent->first_name }}</td>
                <td>
                    @if($telesalesAgent->status == 1)
                        <button class="btn btn-success">Active</button>
                    @else
                        <button class="btn btn-danger">Inactive</button>
                    @endif
                </td>
                <td>{{ $telesalesAgent->username }}</td>
                <td>
                    @if($telesalesAgent->islogin == 1)
                        <button class="btn btn-success">Logged In</button>
                    @else
                        <button class="btn btn-danger">Logged Out</button>
                    @endif
                </td>
                <td>{{ $telesalesAgent->call_status }}</td>
                <td>{{ $telesalesAgent->today_login_time }}</td>
                <td>{{ $telesalesAgent->today_logout_time }}</td>
                <td>{{ $telesalesAgent->email }}</td>
                <td>{{ $telesalesAgent->company_id }}</td>
                <td>
                    {{-- <a href="{{ route('telesales-agents.show', $telesalesAgent->agent_id) }}" class="btn btn-info">View</a> --}}

                    <a href="{{ route('telesales-agents.edit', $telesalesAgent->agent_id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('telesales-agents.destroy', $telesalesAgent->agent_id) }}" method="post" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No telesales agents available.</p>
    @endif
</div>

<script>
let table = new DataTable('#agents');
</script>



@endsection
