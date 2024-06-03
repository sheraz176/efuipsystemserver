@extends('superadmin.layout.master')
@include('superadmin.partials.style')
@section('content')
    <div class="">
        <div class="ms-content-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb pl-0">
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i
                                        class="material-icons"></i>Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Deshboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Telesales Agent</li>
                        </ol>
                    </nav>
                    <div class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome align-items-center">
                            <h6>Telesales Agent </h6>
                            <a href="{{ route('telesales-agents.create') }}" data-toggle="modal"
                                class="btn btn-primary d-inline w-20" type="submit">Create New Telesales Agent</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-12">
                    <div class="ms-card">
                        <div class="ms-card-body">
                            @if (count($telesalesAgents) > 0)
                                <table id="myTable" class="display" style="width:100%">
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
                                        @foreach ($telesalesAgents as $telesalesAgent)
                                            <tr>
                                                <td>{{ $telesalesAgent->agent_id }}</td>
                                                <td>{{ $telesalesAgent->first_name }}</td>
                                                <td>
                                                    @if ($telesalesAgent->status == 1)
                                                        <button class="btn btn-success">Active</button>
                                                    @else
                                                        <button class="btn btn-danger">Inactive</button>
                                                    @endif
                                                </td>
                                                <td>{{ $telesalesAgent->username }}</td>
                                                <td>
                                                    @if ($telesalesAgent->islogin == 1)
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

                                                    <a href="{{ route('telesales-agents.edit', $telesalesAgent->agent_id) }}"
                                                        class="btn btn-warning">Edit</a>
                                                    <form
                                                        action="{{ route('telesales-agents.destroy', $telesalesAgent->agent_id) }}"
                                                        method="post" style="display: inline;">
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
                    </div>
                </div>
            </div>
        </div>
    </div>




    @include('superadmin.partials.script')

@endsection
