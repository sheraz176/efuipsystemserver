@extends('agent.layout.master')

@section('content')
@if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <h6 class="pb-1 mb-4 text-muted">Active Policies Sold & Active</h6>
<div class="table-responsive">

@if(count($transactions) > 0)
  <table id="subscription" class="table table-hover">
    <thead>
      <tr>
        <th>Transaction ID</th>
        <th>Subscription Time</th>
        <th>Customer Number</th>
        <th>Amount</th>
        <th>Plan ID</th>
        <th>Product ID</th>
        <th>Policy Status</th>

        
      </tr>
    </thead>

    <tbody>
    @foreach($transactions as $transaction)
      <tr>
        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <span class="fw-medium">{{ $transaction->cps_transaction_id }}</span></td>
        <td>{{ $transaction->subscription_time}}</td>
        <td>{{ $transaction->subscriber_msisdn}}</td>
        <td>{{ $transaction->transaction_amount }}</td>
        <td>
            @if($transaction->plan_id == 1)
                Term Takaful Plus
            @elseif($transaction->plan_id == 2)
                Term Takaful Plus & Health
            @elseif($transaction->plan_id == 3)
                Income Protection 
            @else
                {{-- Handle other cases if needed --}}
                Unknown Plan
            @endif
        </td>

        <td>{{ $transaction->productId }}</td>
        <td>@if($transaction->policy_status == 1)
        Active
    @else
        Inactive
    @endif</td>
        
      </tr>

      @endforeach
    </tbody>
  </table>
  @else
  <p>No Sales found.</p>
  @endif
</div>





 @endsection()        