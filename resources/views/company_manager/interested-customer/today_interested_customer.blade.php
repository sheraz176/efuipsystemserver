@extends('company_manager.layout.master')

@section('content')
<div class="">
    <h2>Today Interested Customer</h2>

    @if(count($customer) > 0)
    <table id="agents" class="table table-striped">
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


            @foreach($customer as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->customer_msisdn }}</td>
                <td>{{ $customer->customer_cnic }}</td>
                <td>{{ $customer->beneficiary_msisdn }}</td>
                <td>{{ $customer->beneficiary_cnic }}</td>
                <td>{{ $customer->relationship }}</td>
                <td>{{ $customer->beneficinary_name }}</td>

                <td>
                    @if($customer->deduction_applied == 1)
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

<script>
let table = new DataTable('#agents');
</script>



@endsection
