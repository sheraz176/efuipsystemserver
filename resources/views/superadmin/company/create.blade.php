@extends('superadmin.layout.master')

@section('content')
<div class="container">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Company/</span> Create New Company</h4>
        
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Basic Layout</h5>
                        <small class="text-muted float-end">Company Information</small>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('company.store') }}">
                            @csrf
                            <div class="row">
                                <!-- First Column -->
                                <div class="col-md-6">
                                    <!-- Company ID -->
                                    <div class="form-group">
                                        <label for="company_id">Company ID:</label>
                                        <input type="text" name="company_id" class="form-control" required>
                                    </div>

                                    <!-- Company Name -->
                                    <div class="form-group">
                                        <label for="company_name">Company Name:</label>
                                        <input type="text" name="company_name" class="form-control" required>
                                    </div>

                                    <!-- Company Code -->
                                    <div class="form-group">
                                        <label for="company_code">Company Code:</label>
                                        <input type="text" name="company_code" class="form-control" required>
                                    </div>

                                    <!-- Company Address -->
                                    <div class="form-group">
                                        <label for="company_address">Company Address:</label>
                                        <textarea name="company_address" class="form-control" required></textarea>
                                    </div>
                                </div>

                                <!-- Second Column -->
                                <div class="col-md-6">
                                    <!-- POC Name -->
                                    <div class="form-group">
                                        <label for="company_poc_name">POC Name:</label>
                                        <input type="text" name="company_poc_name" class="form-control" required>
                                    </div>

                                    <!-- POC Number -->
                                    <div class="form-group">
                                        <label for="company_poc_number">POC Number:</label>
                                        <input type="text" name="company_poc_number" class="form-control" required>
                                    </div>

                                    <!-- Company Email -->
                                    <div class="form-group">
                                        <label for="company_email">Company Email:</label>
                                        <input type="email" name="company_email" class="form-control" required>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="form-group">
                                        <label for="company_phone_number">Phone Number:</label>
                                        <input type="text" name="company_phone_number" class="form-control" required>
                                    </div>

                                    <!-- Company Status -->
                                    <div class="form-group">
                                        <label for="company_status">Company Status:</label>
                                        <select name="company_status" class="form-select" required>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Create Company Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
