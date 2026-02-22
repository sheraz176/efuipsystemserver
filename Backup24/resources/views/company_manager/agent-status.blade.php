@extends('company_manager.layout.master')
@include('superadmin.partials.style')
@section('content')
    <div class="">
        <div class="ms-content-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb pl-0">
                            <li class="breadcrumb-item"><a href="{{ route('company-manager-dashboard') }}"><i
                                class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company-manager-dashboard') }}">Deshboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Telesales Agent</li>
                        </ol>
                    </nav>
                    <div class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome align-items-center">
                            <h6>Telesales Agent </h6>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($telesalesAgents as $telesalesAgent)
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
