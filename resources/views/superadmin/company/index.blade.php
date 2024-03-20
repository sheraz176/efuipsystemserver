@extends('superadmin.layout.master')

@section('content')

<h2>Company Profiles</h2>

    <a href="{{ route('company.create') }}" class="btn btn-primary" style="margin-bottom:10px">Create Company Profile</a>

    <table class="table" id="company">
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

<script>
let table = new DataTable('#company');
</script>

@endsection