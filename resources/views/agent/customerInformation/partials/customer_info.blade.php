@foreach($customers as $customer)
    <div class="ms-panel">
        <div class="ms-panel-header ms-panel-custome align-items-center">
            <h6>Customer Claims Information (Subscription ID: {{ $customer->subscription_id }})</h6>
        </div>
        <div class="ms-panel-body">
            <div class="row">
                <div class="col-md-3 d-flex align-items-center">
                    <div class="wrapper-line">
                        <div class="admin-avatar">
                            <img src="{{ asset('/assets/img/logo.png')}}">
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="user-detail">
                        <h6 class="custom-heading mb-3">Subscriber Msisdn {{ $customer->subscriber_msisdn }}</h6>
                    </div>
                    <div class="row admin-detail">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subscription_id">Subscription ID</label>
                                <input type="text" class="form-control" id="subscription_id" value="{{ $customer->subscription_id }}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Transaction Amount</label>
                                <input type="text" class="form-control" id="transaction_amount" value="{{ $customer->transaction_amount }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row admin-detail">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subscription_id">Reference ID</label>
                                <input type="text" class="form-control" id="referenceId" value="{{ $customer->referenceId }}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Transaction ID</label>
                                <input type="text" class="form-control" id="cps_transaction_id" value="{{ $customer->cps_transaction_id }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="policy_status">Response</label>
                        <input type="text" class="form-control" id="cps_response_text" value="{{ $customer->cps_response_text }}" readonly disabled>
                    </div>
                    <div class="row admin-detail">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subscription_id">Plan Name</label>
                                <input type="text" class="form-control" id="plan_id" value="{{ $customer->plan->plan_name}}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Product Name</label>
                                <input type="text" class="form-control" id="productId" value="{{ $customer->products->product_name }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row admin-detail">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subscription_id">Duration</label>
                                <input type="text" class="form-control" id="product_duration" value="{{ $customer->product_duration }}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Company Name</label>
                                <input type="text" class="form-control" id="company_id"
                                       value="{{ isset($customer->companyProfiles) ? $customer->companyProfiles->company_name : 'Not Defined' }}"
                                       readonly disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="policy_status">Policy Status</label>
                        <input type="text"
                               class="form-control"
                               id="policy_status"
                               value="{{ $customer->policy_status == 1 ? 'Active' : 'Inactive' }}"
                               readonly
                               disabled
                               style="background-color: {{ $customer->policy_status == 1 ? 'green' : 'red' }}; color: white;">
                    </div>
                    <div class="row admin-detail">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="subscription_id">Sales Agent</label>
                                <input type="text" class="form-control" id="sales_agent" value="{{ $customer->teleSalesAgent->username }}" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_amount">Subscription Time</label>
                                <input type="text" class="form-control" id="subscription_time" value="{{ $customer->subscription_time }}" readonly disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="policy_status">Consent</label>
                            <input type="text" class="form-control" id="consent" value="{{ $customer->consent }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <form method="POST" action="{{ route('superadmin.claim.submit') }}" class="mt-4">
    @csrf
    <input type="hidden" name="msisdn" value="{{ $customer->subscriber_msisdn }}">
    <input type="hidden" name="plan_id" value="{{ $customer->plan_id }}">
    <input type="hidden" name="product_id" value="11"> {{-- Hardcoded as per requirement --}}

    <div class="form-group">
        <label for="type">Claim Type</label>
        <select name="type" class="form-control" required>
            <option value="">Select Claim Type</option>
            <option value="hospitalization">Hospitalization</option>
            <option value="medical_and_lab_expense">Medical & Lab Expense</option>
        </select>
    </div>
        <br>
    <button type="submit" class="btn btn-primary">Submit Claim</button>
</form>

@endforeach

