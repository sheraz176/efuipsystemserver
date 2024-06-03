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
                            <li class="breadcrumb-item active" aria-current="page">Telesales Agents</li>
                        </ol>
                    </nav>
                    <div class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome align-items-center">
                            <h6>Telesales Agents</h6>
                            <a href="{{ route('superadmin.super_agent_create') }}" data-toggle="modal"
                                class="btn btn-primary d-inline w-20" type="submit">Create Telesales Super Agents</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-12">
                    <div class="ms-card">
                        <div class="ms-card-body">
                            @if (count($super_agents) > 0)
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

                                        @foreach ($super_agents as $super_agents)
                                            <tr>
                                                <td>{{ $super_agents->super_agent_id }}</td>
                                                <td>{{ $super_agents->first_name }}</td>
                                                <td>
                                                    @if ($super_agents->status == 1)
                                                        <button class="btn btn-success">Active</button>
                                                    @else
                                                        <button class="btn btn-danger">Inactive</button>
                                                    @endif
                                                </td>
                                                <td>{{ $super_agents->username }}</td>
                                                <td>
                                                    @if ($super_agents->islogin == 1)
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
                    </div>
                </div>
            </div>
        </div>
    </div>





    @include('superadmin.partials.script')

@endsection
