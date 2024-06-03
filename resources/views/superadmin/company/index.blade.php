@extends('superadmin.layout.master')
@include('superadmin.partials.style')
@section('content')
        <div class="ms-content-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb pl-0">
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i
                                        class="material-icons"></i>Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Deshboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Company Profiles</li>
                        </ol>
                    </nav>
                    <div class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome align-items-center">
                            <h6>Company Profiles</h6>
                            <a href="{{ route('company.create') }}" data-toggle="modal"
                                class="btn btn-primary d-inline w-20" type="submit">Create Company Profiles</a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-12">
                    <div class="ms-card">
                        <div class="ms-card-body">

                                <table id="myTable" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                           <th>Company ID</th>
                                           <th>Company Name</th>
                                           <th>Company Code</th>
                                           <th>Company Address</th>
                                           <th>POC Name</th>
                                           <th>POC Number</th>
                                            <th>Company Email</th>
                                           <th>Phone Number</th>
                                           <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($companies as $company)
                                        <td>{{ $company->id }}</td>
                                                <td>{{ $company->company_id }}</td>
                                                <td>{{ $company->company_name }}</td>
                                                <td>{{ $company->company_code }}</td>
                                                <td>{{ $company->company_address }}</td>
                                                <td>{{ $company->company_poc_name }}</td>
                                                <td>{{ $company->company_poc_number }}</td>
                                                <td>{{ $company->company_email }}</td>
                                                <td>{{ $company->company_phone_number }}</td>
                                                <td>{{ $company->company_status }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('company.show', $company->id) }}" class="btn btn-info" >View</a>
                                                        <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning" >Edit</a>
                                                        <!-- <form action="{{ route('company.destroy', $company->id) }}" method="post" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </form> -->
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


@include('superadmin.partials.script')
@endsection
