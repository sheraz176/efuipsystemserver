@extends('agent.layout.master')

@section('content')
@if(session('status'))
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
                      <h5 class="card-title">Detail Manual of Telesales Portal </h5>
                      <p class="card-text">PDF and Video Tutorial of Telesales Portal is available.</p>
                      <a href="{{ asset('/assets/pdf/Manual.pdf') }}" target="_blank" class="btn btn-lg btn-primary">Check Now</a>
                    </div>
                    <div class="card-footer text-muted">Wanted To Know More</div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                <div class="card text-center">
                    <div class="card-header">Create a New Sale</div>
                    <div class="card-body">
                      <h5 class="card-title">Multiple JazzCash Product are Available</h5>
                      <p class="card-text">You have to get the consent of Customer and MPIN.</p>
                      <a href="{{ route('agent.transaction') }}" id="transactionpage" class="btn btn-lg btn-success">Start Subscription/Deduction</a>

                    </div>
                    <div class="card-footer text-muted">Wanted To Know More</div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-3">
                  <div class="card text-center">
                    <div class="card-header">Agent Report</div>
                    <div class="card-body">
                      <h5 class="card-title">Agents Personal Reports </h5>
                      <p class="card-text">Report Contains Daily Login/Logout Report & Sales</p>
                      <a href="{{ route('agent.sucesssales') }}" class="btn btn-lg btn-primary">Check Now</a>
                    </div>
                    <div class="card-footer text-muted">Wanted To Know More</div>
                  </div>
                </div>
              </div>

       

@endsection()