@foreach($customers as $customer)
    <div class="ms-panel">
        <div class="ms-panel-header ms-panel-custome align-items-center">
            <h6>Manage Refunds (Subscription ID: {{ $customer->subscription_id }})</h6>
        </div>
        <div class="ms-panel-body">
            <div class="row">

                <div class="col-md-12">
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
                               style="background-color: {{ $customer->policy_status == 1 ? 'gray' : 'red' }}; color: white;">
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
                            <label for="policy_status">Reason of Refund</label>
                            <textarea id="reason" class="form-control" name="reason" rows="4" cols="50"></textarea>
                            <br>
                            <div id="refund-error" class="alert alert-danger" style="display: none;"></div> <!-- Error div -->
                            <div id="refund-success" class="alert alert-success" style="display: none;"></div> <!-- Success div -->
                            <button type="button" id="refund-button" class="btn btn-danger btn-sm">Refund</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
