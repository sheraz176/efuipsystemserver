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
                            <li class="breadcrumb-item active" aria-current="page">Today Deduction Interested Customer</li>
                        </ol>
                    </nav>
                    <div class="ms-panel">
                        <div class="ms-panel-header ms-panel-custome align-items-center">
                            <h6>Today Deduction Interested Customer</h6>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-12">
                    <div class="ms-card">
                        <div class="ms-card-body">
                            @if (count($customer) > 0)
                                <table id="myTable" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer Msisdn</th>
                                            <th>Customer Cnic</th>
                                            <th>Beneficiary Msisdn</th>
                                            <th>Beneficiary Cnic</th>
                                            <th>Relationship</th>
                                            <th style="width: 150px;">Beneficinary Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($customer as $customer)
                                            <tr>
                                                <td>{{ $customer->id }}</td>
                                                <td>{{ $customer->customer_msisdn }}</td>
                                                <td>{{ $customer->customer_cnic }}</td>
                                                <td>{{ $customer->beneficiary_msisdn }}</td>
                                                <td>{{ $customer->beneficiary_cnic }}</td>
                                                <td>{{ $customer->relationship }}</td>
                                                <td>{{ $customer->beneficinary_name }}</td>

                                                <td>
                                                    @if ($customer->deduction_applied == 1)
                                                        <button class="btn btn-success">Deduction Applied</button>
                                                    @else
                                                        <button class="btn btn-danger">Deduction Not Applied</button>
                                                    @endif
                                                </td>


                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No Interested Customer available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('superadmin.partials.script')






@endsection
