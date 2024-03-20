@extends('superadmin.layout.master')

@section('content')
<div class="container">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Tele Sales Agents/</span> Edit Agent</h4>
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Basic Layout</h5>
                        <small class="text-muted float-end">Company Information</small>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('telesales-agents.update', $telesalesAgent->agent_id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name:</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $telesalesAgent->first_name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name:</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $telesalesAgent->last_name }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username">Username:</label>
                                        <input type="text" name="username" class="form-control" value="{{ $telesalesAgent->username }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="email" name="email" class="form-control" value="{{ $telesalesAgent->email }}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" name="password" class="form-control">
                                    <small class="text-muted">Leave blank if you don't want to change the password.</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status:</label>
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="status" value="1" {{ $telesalesAgent->status == 1 ? 'checked' : '' }}> Active
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="status" value="0" {{ $telesalesAgent->status == 0 ? 'checked' : '' }}> Inactive
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_id">Company ID:</label>
                                        <select name="company_id" class="form-select" required>
                                            <!-- Fetch and loop through companies from the database to populate the dropdown -->
                                            @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ $telesalesAgent->company_id == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Add other form fields as needed -->

                            <button type="submit" class="btn btn-primary">Update Telesales Agent</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
