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
                        <form method="post" action="{{ route('superadmin.telesales-agents.update.emp') }}">
                            @csrf
                            <input type="hidden" name="id" class="form-control" value="{{$telesalesAgent->agent_id}}" required>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="first_name">Emp Code:</label>
                                        <input type="text" name="emp_code" class="form-control" value="{{ $telesalesAgent->emp_code}}" required>
                                    </div>
                                </div>

                            </div>
                          <br>

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
