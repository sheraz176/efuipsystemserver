@extends('agent.layout.master')

@section('content')

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
     $(document).ready(function () {
      let tablefailed = $('#FailedSubscriptions').DataTable();
      console.log(tablefailed);
   });
</script>

@if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <h6 class="pb-1 mb-4 text-muted">Failed Sales Information</h6>
<div class="table-responsive">

@if(count($Failedtransactions) > 0)
  <table id="FailedSubscriptions" class="table table-hover">
    <thead>
      <tr>
        <th>Transaction ID</th>
        <th>Subscription Time</th>
        <th>Failed Message</th>
        <th>Amount</th>
        <th>Customer Number</th>
        <th>Product ID</th>
        <th>Plan ID</th>

        
      </tr>
    </thead>

    <tbody>
    @foreach($Failedtransactions as $Failedtransaction)
      <tr>
        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <span class="fw-medium">{{ $Failedtransaction->transactionId }}</span></td>
        <td>{{ $Failedtransaction->timeStamp}}</td>
        <td>{{ $Failedtransaction->failedReason}}</td>
        <td>{{ $Failedtransaction->amount }}</td>
        <td>{{ $Failedtransaction->accountNumber }}</td>
        <td>
    @if ($Failedtransaction->product_id == 1)
        Annual Subscription
    @elseif ($Failedtransaction->product_id == 2)
        Monthly Subscription
    @elseif ($Failedtransaction->product_id == 3)
        Daily Subscription
    @else
        Unknown Subscription Type
    @endif
</td>
        <td>{{ $Failedtransaction->planId }}</td>
        
      </tr>

      @endforeach
    </tbody>
  </table>
  @else
  <p>No Sales found.</p>
  @endif
</div>


 @endsection()        