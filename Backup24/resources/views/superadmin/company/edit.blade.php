<!-- resources/views/company/edit.blade.php -->

@extends('superadmin.layout.master')

@section('content')
    <div class="container">
        <h2>Edit Company Profile</h2>
        
        <form method="post" action="{{ route('company.update', $company->id) }}">
            @csrf
            @method('PATCH')

            <div class="row">
                <!-- First Column -->
                <div class="col-md-6">
                    <!-- Company ID -->
                    <div class="form-group">
                        <label for="company_id">Company ID:</label>
                        <input type="text" name="company_id" class="form-control" value="{{ $company->company_id }}" required>
                    </div>

                    <!-- Company Name -->
                    <div class="form-group">
                        <label for="company_name">Company Name:</label>
                        <input type="text" name="company_name" class="form-control" value="{{ $company->company_name }}" required>
                    </div>

                    <!-- Company Code -->
                    <div class="form-group">
                        <label for="company_code">Company Code:</label>
                        <input type="text" name="company_code" class="form-control" value="{{ $company->company_code }}" required>
                    </div>

                    <!-- Company Address -->
                    <div class="form-group">
                        <label for="company_address">Company Address:</label>
                        <textarea name="company_address" class="form-control" required>"{{ $company->company_address }}"</textarea>
                    </div>
                </div>
                
                <!-- Second Column -->
                <div class="col-md-6">
                    <!-- POC Name -->
                    <div class="form-group">
                        <label for="company_poc_name">POC Name:</label>
                        <input type="text" name="company_poc_name" class="form-control" value="{{ $company->company_poc_name }}" required>
                    </div>

                    <!-- POC Number -->
                    <div class="form-group">
                        <label for="company_poc_number">POC Number:</label>
                        <input type="text" name="company_poc_number" class="form-control" value="{{ $company->company_poc_number }}" required>
                    </div>

                    <!-- Company Email -->
                    <div class="form-group">
                        <label for="company_email">Company Email:</label>
                        <input type="email" name="company_email" class="form-control" value="{{ $company->company_email }}" required>
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="company_phone_number">Phone Number:</label>
                        <input type="text" name="company_phone_number" class="form-control" value="{{ $company->company_phone_number }}" required>
                    </div>

                    <!-- Company Status -->
                    <div class="form-group">
                        <label for="company_status">Company Status:</label>
                        <select name="company_status" class="form-control" required>
                            <option value="Active" {{ $company->company_status === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $company->company_status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update Company Profile</button>
        </form>
    </div>
@endsection
