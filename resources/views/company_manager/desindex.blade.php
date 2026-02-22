@extends('company_manager.layout.master')
@include('superadmin.partials.style')

@section('content')

<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-5">
                    <div class="card-header">{{ __('Company Manager Dashboard') }}</div>

                    <div class="card-body">
                        <p>Welcome to the dashboard, {{ Auth::guard('company_manager')->user()->username }}!</p>
                        <a href="{{ route('company.manager.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('company.manager.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('superadmin.partials.script')



@endsection
