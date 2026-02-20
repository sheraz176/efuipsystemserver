@extends('superadmin.layout.master')
@include('superadmin.partials.style')

@section('content')
<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Update Commands </li>
                </ol>
            </nav>

            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row mb-3">

                    </div>
                </div>
                <div class="ms-panel-body">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif


                        <form method="POST" action="{{ route('schedule.update') }}">
        @csrf
        <div class="mb-3">
            <label>Command Time</label>
            <input type="time" name="run_time" value="{{ $command->run_time }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $command->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$command->is_active ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>

    <form method="POST" action="{{ route('schedule.runNow') }}" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-success">Run Command Now</button>
    </form>

                </div>
            </div>

        </div>
    </div>
</div>



@include('superadmin.partials.script')
@endsection
