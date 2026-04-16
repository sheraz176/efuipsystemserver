@extends('basic-agent-l.layout.master')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <h6 class="pb-1 mb-4 text-muted">Live Deduction Platform</h6>
    <div class="row mb-5">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card text-center">
                <div class="card-header">Portal Information</div>
                <div class="card-body">
                    <h5 class="card-title">Detail Manual of Zindigi Portal </h5>
                    <p class="card-text">PDF and Video Tutorial of Zindigi Portal is available.</p>
                    <a href="{{ asset('/assets/pdf/Manual.pdf') }}" target="_blank" class="btn btn-lg btn-primary">Check
                        Now</a>
                </div>
                <div class="card-footer text-muted"></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card text-center" style="border-color:#81CECA;">
                <div class="card-header" style="background-color:#81CECA; color:#fff;">
                    Create a New Sale
                </div>

                <div class="card-body">
                    <h5 class="card-title" style="color:#81CECA;">
                        Multiple Zindigi Product are Available
                    </h5>

                    <p class="card-text">
                        You have Provide Product Information to Customer All Benfits and Product Price.
                    </p>

                    <a href="{{ route('basic-agent-l.transaction') }}" id="transactionpage" class="btn btn-lg"
                        style="background-color:#81CECA; border-color:#81CECA; color:#fff;">
                        Start Subscription/Deduction
                    </a>
                </div>

                <div class="card-footer text-muted"></div>
            </div>
        </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card text-center">
                    <div class="card-header">Agent Report</div>
                    <div class="card-body">
                        <h5 class="card-title">Agents Personal Reports </h5>
                        <p class="card-text">Report Contains Daily Login/Logout Report & Sales</p>
                        <a href="{{ route('basic-agent-l.sucesssales') }}" class="btn btn-lg btn-primary">Check Now</a>
                    </div>
                    <div class="card-footer text-muted"></div>
                </div>
            </div>
        </div>
    @endsection()
