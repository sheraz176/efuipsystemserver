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


@foreach($commands as $command)
<form method="POST" action="{{ route('schedule.update') }}" class="mb-4">
    @csrf

    <input type="hidden" name="command_name" value="{{ $command->command_name }}">

    <h5>{{ $command->command_name }}</h5>

    <div class="mb-2">
        <label>Command Time</label>
        <input type="time" name="run_time" value="{{ $command->run_time }}" class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Status</label>
        <select name="is_active" class="form-control">
            <option value="1" {{ $command->is_active ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !$command->is_active ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endforeach
    

                </div>
            </div>

        </div>
    </div>
</div>



@include('superadmin.partials.script')
@endsection
