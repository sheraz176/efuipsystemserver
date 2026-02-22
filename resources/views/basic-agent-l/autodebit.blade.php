<!-- resources/views/super_agent/customer_form.blade.php -->

@extends('basic-agent-l.layout.master')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h4 class="mb-4"><span class="text-muted fw-light">Auto Debit /</span> Search Customer</h4>
                <form id="customerSearchForm">
                    @csrf
                    <div class="form-group" style="padding-bottom: 10px;">
                        <label for="customerMSISDN">Customer MSISDN</label>
                        <input type="text" class="form-control" value="{{ $existingRequest->msisdn ?? '' }}"
                            id="customerMSISDN" name="customer_msisdn">
                    </div>
                    <button type="submit" class="btn btn-primary">Search Customer</button>
                </form>

            </div>
        </div>

        <hr>
        <div id="errorMessageSection" class="text-danger"></div>
        <div class="row mt-3" id="customerDataSection" style="display: none;">
            <div class="col-md-12">
                <h4 class="mb-4"><span class="text-muted fw-light">Auto Debit /</span> Customer Data for Auto Debit
                    Payment</h4>
                <input type="hidden" class="form-control" value="(DTMF),1" id="consents" name="consent" readonly>

                 <input type="hidden" class="form-control"  id="plan_id" name="plan_id" >
                   <input type="hidden" class="form-control" id="product_id" name="product_id" >
                   <input type="hidden" class="form-control" id="company_id" name="company_id" >
                   <input type="hidden" class="form-control" id="agent_id" name="agent_id" readonly>

                <form id="customerDataForm">
                    <div class="form-group row mb-3">
                        <label for="customerMsisdn" class="col-md-2 col-form-label">Customer MSISDN:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="customerMsisdn" name="customer_msisdn" readonly>
                        </div>
                        <label for="customerCnic" class="col-md-2 col-form-label">Customer CNIC:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="customerCnic" name="customer_cnic" readonly>
                        </div>
                    </div>
                    <!-- Add space between rows -->
                    <div class="form-group row mb-3">
                        <label for="planId" class="col-md-2 col-form-label">Plan Name:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="plan_name" name="plan_name" readonly>
                        </div>
                        <label for="productId" class="col-md-2 col-form-label">Product Name:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="product_name" name="product_name" readonly>
                        </div>
                    </div>
                    <!-- Add space between rows -->
                    <div class="form-group row mb-3">
                        <label for="beneficiaryMsisdn" class="col-md-2 col-form-label">Beneficiary MSISDN:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="beneficiaryMsisdn" name="beneficiary_msisdn"
                                readonly>
                        </div>
                        <label for="beneficiaryCnic" class="col-md-2 col-form-label">Beneficiary CNIC:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="beneficiaryCnic" name="beneficiary_cnic"
                                readonly>
                        </div>
                    </div>
                    <!-- Add space between rows -->
                    <div class="form-group row mb-3">
                        <label for="relationship" class="col-md-2 col-form-label">Relationship:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="relationship" name="relationship" readonly>
                        </div>
                        <label for="beneficiaryName" class="col-md-2 col-form-label">Beneficiary Name:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="beneficiaryName" name="beneficinary_name"
                                readonly>
                        </div>
                    </div>
                    <!-- Add space between rows -->
                    <div class="form-group row mb-3">
                        <label for="deductionApplied" class="col-md-2 col-form-label">Deduction Applied:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="deductionApplied" name="deduction_applied"
                                readonly>
                        </div>
                        <label for="agentId" class="col-md-2 col-form-label">Agent Name:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="agent_name" name="agent_name" readonly>
                        </div>
                    </div>
                    <!-- Add space between rows -->
                    <div class="form-group row mb-3">
                        <label for="companyId" class="col-md-2 col-form-label">Company Name:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="company_name" name="company_name" readonly>
                        </div>
                        <label for="superAgentId" class="col-md-2 col-form-label">Super Agent ID:</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="superAgentId" name="super_agent_id" readonly>
                        </div>
                    </div>
                    <!-- Add other form fields as needed -->

                         <div class="d-flex gap-2">
                        <button id="consent" class="btn btn-danger" type="button">
                            Check Consent (DTMF)
                        </button>

                        <div id="autoDebitWrapper"></div>
                    </div>

                    <div id="consentMessage" style="color: #1d1a1e"></div>



                </form>
            </div>
        </div>
        <div class="row mt-3" id="errorMessageSection" style="display: none;">
            <div class="col-md-12">
                <label for="error" class="text-danger" id="errorMessage">The customer wasn't found or the deduction
                    has already been attempted by the super-agent. According to the rule, only one attempt can be made on
                    the customer's JazzCash wallet <br>
                    موصولہ گاہک نہیں ملا یا کم کرنے کا کوشش کر چکا ہے سپر ایجنٹ نے۔ قاعدے کے مطابق، گاہک کی جاز کیش والٹ پر
                    صرف ایک کوشش کی جا سکتی ہے۔</label>
            </div>
        </div>

        <!-- Success Modal -->


        <div class="modal fade" id="successModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 30%;">
                <form class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h5 class="modal-title">
                            <i class="bi bi-check-circle"
                                style="font-size: 2rem; color: #28a745; vertical-align: middle;"></i>
                            Success Transaction
                        </h5>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    </div>
                    <div class="modal-body mt-3 text-center" id="successModalBody" style="font-size: 1.25rem;">
                        <!-- Adjust text size here -->
                        <!-- Modal body content goes here -->
                    </div>
                    <div class="modal-footer text-center">
                       <a href="{{ route('basic-agent-l.index') }}"> <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            >Proceed to Next Deduction</button> </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="failedModal" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 30%;">
                <form class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle-fill"
                                style="font-size: 2rem; color: #dc3545; vertical-align: middle;"></i>
                            Failed Transaction
                        </h5>
                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                    </div>
                    <div class="modal-body mt-3 text-center" id="failedModalBody" style="font-size: 1.25rem;">
                        <!-- Adjust text size here -->
                        <!-- Modal body content goes here -->
                    </div>
                    <div class="modal-footer text-center">
                        <a href="{{ route('basic-agent-l.index') }}">  <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                            >Proceed to Next Deduction</button> </a>
                    </div>
                </form>
            </div>
        </div>


        <!-- Error Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="errorModalBody">
                        <h6> Internet Connectivity Issue </h6>
                        <br>
                        Contact your Local IT Support and Check Your Internet Connectivity
                        <!-- Error message will appear here -->
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


     <script>
        $(document).ready(function() {
            $('#consent').click(function() {
                var msisdn = $('#customerMsisdn').val();

                $.ajax({
                    url: '{{ route('basic-agent-l.consent_check') }}',
                    method: 'POST',
                    data: {
                        msisdn: msisdn,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.consistent_provider == 1) {
                            // Only append button if not already present
                            if ($('#autoDebitButton').length === 0) {
                                $('#autoDebitWrapper').html(`
                                    <button id="autoDebitButton" class="btn btn-primary" type="button">
                                        <span id="buttonText">Proceed to Auto Debit</span>
                                        <span id="buttonLoader" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                                    </button>
                                `);
                            }
                        } else {
                            $('#autoDebitWrapper').empty(); // Remove if invalid
                        }

                        $('#consentMessage').text(response.message);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Use event delegation for dynamically created button
      $(document).on('click', '#autoDebitButton', function() {
                // Example Action: You can replace this with your actual logic
                // alert('Auto Debit action triggered!');
                 // disableAutoDebitButton();

            // Show the loader and hide the button text
            $('#buttonText').hide();
            $('#buttonLoader').show();

            // Get the values from form fields
            var customer_msisdn = $('#customerMsisdn').val();
            var customer_cnic = $('#customerCnic').val();
            var plan_id = $('#plan_id').val();
            var product_id = $('#product_id').val();
            var beneficiary_msisdn = $('#beneficiaryMsisdn').val();
            var beneficiary_cnic = $('#beneficiaryCnic').val();
            var beneficinary_name = $('#beneficiaryName').val();
            var company_id = $('#company_id').val();
            var agent_id = $('#agent_id').val();
            var consents = $('#consents').val();

            // Construct the data object
            var requestData = {
                subscriber_msisdn: customer_msisdn,
                customer_cnic: customer_cnic,
                plan_id: plan_id,
                product_id: product_id,
                beneficiary_msisdn: beneficiary_msisdn,
                beneficiary_cnic: beneficiary_cnic,
                beneficinary_name: beneficinary_name,
                agent_id: agent_id,
                company_id: company_id,
                consent: consents
            };

            // Perform AJAX call to ivr_subscription endpoint
            $.ajax({
                type: 'POST',
                url: '{{ route('AutoDebitSubscription') }}',
                data: requestData,
                success: function(response) {
                    // Handle success response
                    $('#customerDataForm')[0].reset(); // Reset the form
                    // Display success modal with response data
                    $('#successModalBody').html(response.data.message);
                    $('#successModal').modal('show');
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Handle error response
                    $('#customerDataForm')[0].reset(); // Reset the form
                    if (xhr.status === 422) {
                        // Display failed modal with error message
                        $('#failedModalBody').html(xhr.responseJSON.data.message);
                        $('#failedModal').modal('show');
                    } else {
                        // Display error modal with error message
                        $('#errorModal').modal('show');
                    }
                },
                complete: function() {
                    // Hide the loader and show the button text again
                    $('#buttonText').show();
                    $('#buttonLoader').hide();

                    // Re-enable the button after the request is complete
                    enableAutoDebitButton();
                }
            });

                // Perform your AJAX call here
            });
        });
    </script>

    <script>
        var agentId;
        var companyId;
        var planId;
        var productId;

        function disableAutoDebitButton() {
            $('#autoDebitButton').prop('disabled', true);
        }

        // Function to enable the button
        function enableAutoDebitButton() {
            $('#autoDebitButton').prop('disabled', false);
        }

        $(document).ready(function() {
    function fetchCustomerData(msisdn) {
        // Check if the MSISDN is exactly 11 digits
        if (msisdn.length === 11) {
            var formData = {
                customer_msisdn: msisdn,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            };

            $.ajax({
                type: 'POST',
                url: '{{ route('basic-agent-l.fetch_customer_data') }}',
                data: formData,
                success: function(data) {
                    if (data) {
                        // Populate the fields with the returned data
                        $('#customerDataSection').show();
                        $('#customerMsisdn').val(data.customer_msisdn);
                        $('#customerCnic').val(data.customer_cnic);
                        $('#plan_name').val(data.plan_name);
                        $('#product_name').val(data.product_name);
                        $('#plan_id').val(data.plan_id);
                        $('#product_id').val(data.product_id);
                        $('#agent_id').val(data.agent_id);
                        $('#company_id').val(data.company_id);


                        $('#beneficiaryMsisdn').val(data.beneficiary_msisdn);
                        $('#beneficiaryCnic').val(data.beneficiary_cnic);
                        $('#relationship').val(data.relationship);
                        $('#beneficiaryName').val(data.beneficinary_name);
                        $('#deductionApplied').val(data.deduction_applied);
                        $('#agent_name').val(data.agent_name);
                        $('#company_name').val(data.company_name);
                        $('#consistent_provider').val(data.consistent_provider);
                        $('#superAgentId').val('{{ session('agent')->username }}');

                        $('#errorMessageSection').hide();
                        $('#customerDataSection').show();

                        // Additional checks if needed
                        // checkAutoDebitStatus(data.customer_msisdn);
                    } else {
                        // Display 'Data not found' if response is empty
                        $('#customerDataSection').hide();

                        $('#errorMessageSection').text("The customer wasn't found or the deduction has already been attempted by the super-agent. According to the rule, only one attempt can be made on the customer's JazzCash wallet.\nموصولہ گاہک نہیں ملا یا کم کرنے کا کوشش کر چکا ہے سپر ایجنٹ نے۔ قاعدے کے مطابق، گاہک کی جاز کیش والٹ پر صرف ایک کوشش کی جا سکتی ہے۔");

                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    // Hide the customer data section and show error
                    $('#customerDataSection').hide();
                    $('#errorMessageSection').text("The customer wasn't found or the deduction has already been attempted by the super-agent. According to the rule, only one attempt can be made on the customer's JazzCash wallet.\nموصولہ گاہک نہیں ملا یا کم کرنے کا کوشش کر چکا ہے سپر ایجنٹ نے۔ قاعدے کے مطابق، گاہک کی جاز کیش والٹ پر صرف ایک کوشش کی جا سکتی ہے۔");

                }
            });
        } else {
            // Hide customer data and show an error if MSISDN is not 11 digits
            $('#customerDataSection').hide();
            $('#errorMessageSection').text('Please enter a valid 11-digit MSISDN').show();
        }
    }

    // Check if the customerMSISDN input already has a value on page load
    var initialMsisdn = $('#customerMSISDN').val().trim();
    if (initialMsisdn.length === 11) {
        fetchCustomerData(initialMsisdn);
    }

    // Trigger data fetching automatically as the user types
    $('#customerMSISDN').on('input', function() {
        var msisdn = $(this).val().trim();
        fetchCustomerData(msisdn);
    });

    $('#customerSearchForm').submit(function(event) {
        event.preventDefault();
        var msisdn = $('#customerMSISDN').val().trim();
        fetchCustomerData(msisdn);
    });
});




           </script>
@endpush
