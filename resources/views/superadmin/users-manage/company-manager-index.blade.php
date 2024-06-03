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
                        <li class="breadcrumb-item active" aria-current="page">Companies Managers</li>
                    </ol>
                </nav>
                <div class="ms-panel">
                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <h6>Companies Managers</h6>
                        <a href="{{ route('superadmin.company_manager_create') }}" data-toggle="modal"
                            class="btn btn-primary d-inline w-20" type="submit">Create Companies Managers</a>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-md-12">
                <div class="ms-card">
                    <div class="ms-card-body">
                        @if(count($companies_managers) > 0)
                            <table id="myTable" class="display" style="width:100%">
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
                </div>
            </div>
        </div>
    </div>
</div>


@include('superadmin.partials.script')

@endsection
