@extends('agent.layout.master')




@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
@endpush

@section('content')


   {{-- <button onclick="smscheck()" type="button" > send testing </button> --}}

<div class="bs-stepper wizard-modern wizard-modern-example">
                        <div class="bs-stepper-header" style="padding-left: 101px; padding-right: 103px;">
                          <div class="step" data-target="#account-details-modern">
                            <button type="button" class="step-trigger">
                              <span class="bs-stepper-circle">1</span>
                              <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Subscriber Information </span>

                              </span>
                            </button>
                          </div>
                          <div class="line" style="background-color: #ffffff00;">
                            <i class="bx bx-chevron-right"></i>
                          </div>
                          <div class="step" data-target="#personal-info-modern">
                            <button type="button" class="step-trigger">
                              <span class="bs-stepper-circle">2</span>
                              <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Beneficinary Information</span>

                              </span>
                            </button>
                          </div>
                          <div class="line" style="background-color: #ffffff00;">
                            <i class="bx bx-chevron-right"></i>
                          </div>
                          <div class="step" data-target="#social-links-modern">
                            <button type="button" class="step-trigger">
                              <span class="bs-stepper-circle">3</span>
                              <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Sale Preview</span>

                              </span>
                            </button>
                          </div>
                        </div>
                        <div class="bs-stepper-content" style="padding-left: 160px;padding-right: 149px;" >
                          <form onSubmit="return false">
                            <!-- Account Details -->
                            <div id="account-details-modern" class="content">
                              <div class="content-header mb-3">
                                <h6 class="mb-0">Subscriber Information</h6>
                                <small>Enter Subscriber Account Details.</small>
                              </div>
                              <div class="row g-3">
                                <div class="col-md-6">
                                <label class="form-label" for="username-modern">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" id="mobile-number" class="form-control" oninput="mobilenumber()" placeholder="03115014142" required/>
				                        <div id="mobile-error" class="text-danger">
				                      </div>
                                </div>

                                <div class="col-md-6">
                                  <label class="form-label" for="email-modern">CNIC Number</label>
                                  <input type="text" id="cnic" class="form-control" placeholder="6110185205253" aria-label="john.doe" required />
                                  <div id="cnic-error" class="text-danger" style="height: 1em;">&nbsp;</div>
                              </div>

                                <div class="col-md-6">
                                  <label class="form-label" for="plan-modern">Active Plans <span class="text-danger">*</span></label>
                                  <select onchange="plannumber()" class="form-select" name="plan" id="plan" required>


                                  @foreach($plan_information as $plan)
                                  <option value="{{ $plan->plan_id }}">{{ $plan->plan_name}}</option>
                                  @endforeach


                                  </select>
                                </div>

                                <div class="col-md-6">
                                  <label class="form-label" for="products-modern">Active Products <span class="text-danger">*</span></label>
                                  <select class="form-select"  name="product" id="product" onchange="copyplancode()" required>

                                  {{-- The product options will be populated dynamically using JavaScript --}}

                                  </select>
                                </div>


                              <div class="col-12 d-flex justify-content-between">
                                  <button class="btn btn-label-secondary btn-prev" disabled> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                  </button>
                                  <button class="btn btn-primary btn-next" onclick="proceedToNext()"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                </div>
                              </div>

                              <div class="row justify-content-center mt-3">
                                  <div class="col-auto text-center">
                                      <p>You have to Enter the Number and Press the Check Button Will Return You Green Signal to Process If Red Text Appear Customer is already Subscribed</p>
                                      <button class="btn btn-primary">Check Customer Subscription & Balance for TERM TAKAFUL</button>
                                  </div>
                              </div>
                            </div>
                            <!-- Personal Info -->
                            <div id="personal-info-modern" class="content">
                              <div class="content-header mb-3">
                                <h6 class="mb-0">Beneficinary Info</h6>
                                <small>Enter Customers Beneficinary Info.</small>
                              </div>
                              <div class="row g-3">
                                <div class="col-md-6">
                                  <label class="form-label" for="first-name-modern">Name <span class="text-danger">*</span></label>
                                  <input type="text" id="beneficiary-name" class="form-control" placeholder="Danish Ahmed" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="last-name-modern">Mobile Number </label>
                                    <input type="text" id="beneficiary-mobile" class="form-control" placeholder="03110554356" required/>
                                    <div id="beneficiary-error" class="text-danger" style="height: 1em;"></div>
                                </div>
                                <div class="col-md-6">
                                  <label class="form-label" for="relationship-modern">Relationship <span class="text-danger">*</span></label>
                                  <select class="form-select" id="relationship-modern" required>

                                    <option>Brother</option>
                                    <option>Sister</option>
                                    <option>Father</option>
                                    <option>Mother</option>
                                    <option>Spouse</option>
                                    <option>Other</option>
                                  </select>
                                </div>

                                <div class="col-md-6">
                                  <label class="form-label" for="beneficiary-cnic-modern">Beneficinary CNIC </label>
                                  <input type="text" id="beneficiary-cnic" class="form-control" placeholder="6110185205253" required/>
                                  <div id="cnicError-Beneficinary" class="error"></div>
                                </div>

                                <div class="col-12 d-flex justify-content-between">
                                  <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                  </button>
                                  <button class="btn btn-primary btn-next" id="nextBtnBeneficiary" disabled onclick="proceedToNextBeneficiary()"> <span class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Next</span> <i class="bx bx-chevron-right bx-sm me-sm-n2"></i></button>
                                </div>
                              </div>
                            </div>
                            <!-- Social Links -->
                            <div id="social-links-modern" class="content">
                              <div class="content-header mb-3">
                                <h6 class="mb-0">Consent</h6>
                                <small>Agent Confirms the MPIN.</small>
                              </div>
                              <div class="row g-3">
                              <div class="">

                      <div class="demo-inline-spacing mt-3">
                        <div class="list-group">
                          <label class="list-group-item">
                            <input class="form-check-input me-1" type="checkbox" checked value="" />
                            I confirm that the customer has granted consent to proceed with the product subscription. The M-PIN is being pushed at the customer's explicit approval to finalize the transaction and deduct the amount from their JazzCash account. Additionally, I have accurately briefed the customer about the product benefits and coverage to ensure a transparent understanding. </label>
                        </div>
                      </div>
                    </div>
                                <div class="col-12 d-flex justify-content-between">
                                  <button class="btn btn-primary btn-prev"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                  </button>
                                  <button class="btn btn-success btn-submit">Submit</button>
                                </div>
                              </div>
                            </div>
                          </form>
                        </div>
                        </div>



<div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
  <div class="modal-dialog" style="max-width: 50%;">
    <form class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="backDropModalTitle">Jazz Cash Transaction</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
        </div>
        <div class="row g-2">
          <div class="col mb-0" >
            <label for="emailBackdrop" class="form-label">Customer MSISDN</label>
            <input type="text" id="m-number" class="form-control" readonly />
          </div>
          <div class="col mb-0">
            <label for="product" class="form-label">PLAN CODE</label>
            <input type="text" id="planCode" class="form-control" readonly />
          </div>

        </div>

        <div class="row g-2" style="padding-top: 3%;">
          <div class="col mb-0">
          <span id="countdown" style="font-weight: bold;"></span>
          <div id="error-message" style="color: red; display: none;">M-PIN Expired Generate New M-PIN After 00:60 Second</div>
          <pre id="json-display"></pre>

          </div>
        </div>

            <div class="row g-2">
              <div class="col mb-0">
                <span id="Subscription_Information" style="font-weight: bold;"></span>
                <div id="error-message" style="color: red;"></div>
              </div>
            </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" onclick="resetForm()">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="makeAjaxRequest()" id="proceedBtn">Proceed With MPIN</button>
      </div>
    </form>
  </div>
</div>



<script>

let modernStepper;


document.addEventListener('DOMContentLoaded', function () {
    const wizardModern = document.querySelector('.wizard-modern-example');

    if (typeof wizardModern !== undefined && wizardModern !== null) {
        const wizardModernBtnNextList = [].slice.call(wizardModern.querySelectorAll('.btn-next')),
            wizardModernBtnPrevList = [].slice.call(wizardModern.querySelectorAll('.btn-prev')),
            wizardModernBtnSubmit = wizardModern.querySelector('.btn-submit');

        // Assign modernStepper globally
        modernStepper = new Stepper(wizardModern, {
            linear: false
        });

        if (wizardModernBtnNextList) {
            wizardModernBtnNextList.forEach(wizardModernBtnNext => {
                wizardModernBtnNext.addEventListener('click', event => {
                    modernStepper.next();
                });
            });
        }
        if (wizardModernBtnPrevList) {
            wizardModernBtnPrevList.forEach(wizardModernBtnPrev => {
                wizardModernBtnPrev.addEventListener('click', event => {
                    modernStepper.previous();
                });
            });
        }
        if (wizardModernBtnSubmit) {
            wizardModernBtnSubmit.addEventListener('click', event => {
                var myModal = new bootstrap.Modal(document.getElementById('backDropModal'));
                myModal.show();
            });
        }
    }
});


function smscheck(){
    // alert('hi');
    var plantext = $('#plan :selected').text();
    var msisdn = "03008758478";
    var amount = "1950";

    fetch('{{ route("sms-delivery-route") }}', {
                    method: 'POST',
                    body: JSON.stringify({ msisdn: msisdn , plantext: plantext ,amount:amount }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

     }

function mobilenumber(){
    var customerValue = document.getElementById('mobile-number').value;
    document.getElementById('m-number').value = customerValue;
    var mobileError = document.getElementById("mobile-error");

    var plancheck = $('#plan').val();
    if (customerValue.length === 11 && plancheck.length > 0) {
        checkmobileagenistpackage();

    }



}
function plannumber(){
    var customerValue = document.getElementById('mobile-number').value;
    document.getElementById('m-number').value = customerValue;
    var mobileError = document.getElementById("mobile-error");

    var plancheck = $('#plan').val();
    if (customerValue.length === 11 && plancheck.length > 0) {
        checkmobileagenistpackage();

    }


}

function checkmobileagenistpackage(){

    var customerValue = document.getElementById('mobile-number').value;
    document.getElementById('m-number').value = customerValue;
    var mobileError = document.getElementById("mobile-error");
    var planid = $('#plan').val();
     var plantext = $('#plan :selected').text();
    //  alert(plantext);
      if (customerValue.length === 11) {
                fetch('{{ route("check-subscription") }}', {
                    method: 'POST',
                    body: JSON.stringify({ msisdn: customerValue , planid: planid }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mobileError.innerHTML = "Customer is Already Subscribed to the  " +plantext ;
                        document.querySelector('.btn-next').disabled = true;
                        document.querySelectorAll('.step button').forEach(button => {
                            button.disabled = true;
                        });
                        document.getElementById('product').disabled = true;


                    } else {
                        mobileError.innerHTML = "<span style='color: green;'>Customer is not subscribed. Please Proceed With Sale Pitch.</span>";
                        document.querySelector('.btn-next').disabled = false;
                        document.querySelectorAll('.step button').forEach(button => {
                            button.disabled = false;
                        });
                        document.getElementById('product').disabled = false;

                    }
                })
                .catch(error => {
                    mobileError.innerHTML = "Error occurred while checking subscription status.";
                });
            } else {
                mobileError.innerHTML = "Mobile number should be 11 digits.";
            }
}


// function copyMobileNumber() {
//     // Get the value from the source input field
//     var customerValue = document.getElementById('mobile-number').value;
//     document.getElementById('m-number').value = customerValue;
//     var mobileError = document.getElementById("mobile-error");


//       if (customerValue.length === 11) {
//                 fetch('{{ route("check-subscription") }}', {
//                     method: 'POST',
//                     body: JSON.stringify({ msisdn: customerValue }),
//                     headers: {
//                         'Content-Type': 'application/json',
//                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
//                     }
//                 })
//                 .then(response => response.json())
//                 .then(data => {
//                     if (data.success) {
//                         mobileError.innerHTML = "Customer is Already Subscribed to the Term Takaful (1600) Product";
//                         document.querySelector('.btn-next').disabled = true;
//                         document.querySelectorAll('.step button').forEach(button => {
//                             button.disabled = true;
//                         });
//                         document.getElementById('product').disabled = true;


//                     } else {
//                         mobileError.innerHTML = "<span style='color: green;'>Customer is not subscribed. Please Proceed With Sale Pitch.</span>";
//                         document.querySelector('.btn-next').disabled = false;
//                         document.querySelectorAll('.step button').forEach(button => {
//                             button.disabled = false;
//                         });
//                         document.getElementById('product').disabled = false;

//                     }
//                 })
//                 .catch(error => {
//                     mobileError.innerHTML = "Error occurred while checking subscription status.";
//                 });
//             } else {
//                 mobileError.innerHTML = "Mobile number should be 11 digits.";
//             }

// }


function copyplancode() {

  const selectedProductDropdown = document.getElementById('product');
    const selectedIndex = selectedProductDropdown.selectedIndex;


    if (selectedIndex !== -1) {
        document.getElementById('planCode').value = selectedProductDropdown.options[selectedIndex].text;
    } else {
      document.getElementById('planCode').value = 'Please Select the Package';
    }



}



//Plans and Products
const plansAndProducts = {!! json_encode($plansAndProducts) !!};
var product_amount = 100; // Replace with your actual amount
var product_productID = '123'; // Replace with your actual productID

// Function to update products dropdown based on the selected plan
function updateProductsDropdown(planId) {
    const productDropdown = document.getElementById('product');

    // Clear existing options
    productDropdown.innerHTML = '<option value="">Select Product</option>';

    // Add options based on the selected plan
    if (plansAndProducts.hasOwnProperty(planId)) {
        plansAndProducts[planId].products.forEach(product => {
            if (product.status === 1) { // Only add active products
                const option = document.createElement('option');
                option.value = product.product_id;
                option.textContent = product.product_code;
                productDropdown.appendChild(option);
            }
        });
    }
     if (productDropdown.options.length > 0) {
        productDropdown.selectedIndex = 0;
    }
}

// Function to handle product selection
function handleProductSelection() {
    const selectedProduct = document.getElementById('product');
    const selectedProductId = selectedProduct.value;

    // Get the amount and product ID for the selected product
    const selectedProductDetails = plansAndProducts[document.getElementById('plan').value].products.find(product => product.product_id == selectedProductId);

    // Update the global variables or use them as needed
    product_amount = selectedProductDetails.fee;
    console.log(product_amount);
    product_productID = selectedProductDetails.product_id;
    duration = selectedProductDetails.duration;

    // Log the selected product details (you can remove this in production)
    console.log("Selected Product Details: ", selectedProductDetails);
}

// Initial load with the default/first plan
const initialPlanId = Object.keys(plansAndProducts)[0];
document.getElementById('plan').value = initialPlanId;
updateProductsDropdown(initialPlanId);

// Event listener for plan change
document.getElementById('plan').addEventListener('change', function () {
    const planId = this.value;
    updateProductsDropdown(planId);
});

// Event listener for product change
document.getElementById('product').addEventListener('change', handleProductSelection);


// Next Button Disabled

function checkFormValidity() {
        const mobileNumber = document.getElementById('mobile-number').value;
        //const cnic = document.getElementById('cnic').value;
        const plan = document.getElementById('plan').value;
        const product = document.getElementById('product').value;

        const isFormValid = mobileNumber && plan && product;

        return isFormValid;
    }

    // Function to enable/disable the "Next" button based on form validity
    function updateNextButton() {
        const nextBtn = document.querySelector('.btn-next');
        nextBtn.disabled = !checkFormValidity();
    }

    // Function to proceed to the next step
    function proceedToNext() {
        if (checkFormValidity()) {
            // Add your logic to handle the next step
            console.log('Proceeding to the next step');
        }
    }

    // Add event listeners for input and select elements
    document.getElementById('mobile-number').addEventListener('input', updateNextButton);
    document.getElementById('cnic').addEventListener('input', updateNextButton);
    document.getElementById('plan').addEventListener('change', updateNextButton);
    document.getElementById('product').addEventListener('change', updateNextButton);

    // Initial check
    updateNextButton();


  // Beneficinary Button Control
  function checkBeneficiaryFormValidity() {
        const beneficiaryName = document.getElementById('beneficiary-name').value;
        //const beneficiaryMobile = document.getElementById('beneficiary-mobile').value;
        //const beneficiaryCnic = document.getElementById('beneficiary-cnic').value;

        const isFormValid = beneficiaryName;

        return isFormValid;
    }

    // Function to enable/disable the "Next" button based on Beneficiary Info form validity
    function updateNextButtonBeneficiary() {
        const nextBtn = document.getElementById('nextBtnBeneficiary');
        nextBtn.disabled = !checkBeneficiaryFormValidity();
    }

    // Function to proceed to the next step for Beneficiary Info
    function proceedToNextBeneficiary() {
        if (checkBeneficiaryFormValidity()) {
            // Add your logic to handle the next step for Beneficiary Info
            console.log('Proceeding to the next step for Beneficiary Info');
        }
    }

    // Add event listeners for input and select elements for Beneficiary Info
    document.getElementById('beneficiary-name').addEventListener('input', updateNextButtonBeneficiary);
    document.getElementById('beneficiary-mobile').addEventListener('input', updateNextButtonBeneficiary);
    document.getElementById('relationship-modern').addEventListener('change', updateNextButtonBeneficiary);
    document.getElementById('beneficiary-cnic').addEventListener('input', updateNextButtonBeneficiary);

    // Initial check for Beneficiary Info
    updateNextButtonBeneficiary();


    //CNIC Validator


    //Count Down


//Transaction
//session('agent')->agent_id

function makeAjaxRequest() {

    // Disable the button to prevent multiple submissions
    $('#proceedBtn').prop('disabled', true);
    var selectedPlanId = document.getElementById('plan').value;
    var plantext = $('#plan :selected').text();
    console.log(product_amount);

    var countdownElement = $('#countdown');
    var subscription_Information = $('#Subscription_Information');
    subscription_Information.html('');

    var errorMessageElement = $('#error-message');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var formData = {
        msisdn: $('#mobile-number').val(),
        beneficiary_name: $('#beneficiary-name').val(),
        planCode: $('#planCode').val(),
        amount: product_amount,
        productID: product_productID,
        agent_id: {{ session('agent')->agent_id }},
        company_id: {{ session('agent')->company_id }},
        duration: duration,
        planID: selectedPlanId
        };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    var msisdn = $('#mobile-number').val()
    var TransactionID;
    var referenceId;
    var resultCode;
    // Show the modal with the countdown and start the timer
    countdownElement.text('M-PIN Expiration Time: 00:60');
    errorMessageElement.hide();
    $('#backDropModal').modal('show');
    startTimer(60);

    $.ajax({
        type: 'POST',
        url: '{{ route("transaction-controller-route") }}',
        data: formData,
        success: function (data) {
            console.log(data);
            stopTimer();
            resultCode = data.resultCode;
             TransactionID = data.transactionId;
            var resultDesc = data.resultDesc;
            referenceId = data.referenceId;
            console.log(resultCode);
            var failedReason = data.failedReason;
          if (resultCode === "0") {
            // Display success message
            countdownElement.html('<span style="color: #697a8d; font-size: 25px;">Transaction Status: </span><span style="color: #66c732; font-size: 25px;">Success!</span><br><span style="color: #697a8d; font-size: 25px;">Sale Status: </span><span style="color: #66c732; font-size: 25px;">Confirmed</span>');
                            ////////
                            var subscription_Information = $('#Subscription_Information');
                            $('#proceedBtn').prop('disabled', true);

                            var error_message = $('#error-message');

                            // Update the content of the span with the new information
                           subscription_Information.html('Transaction Information: ' + TransactionID + '<br>JazzCash Response: <span style="color: #66c732;">' + resultDesc + '</span>');
                            // Display error message (if any)
                            if (failedReason) {
                              error_message.text('Error: ' + failedReason);
                            } else {
                              // Clear error message if there is no error
                              error_message.text('');
                            }



                            ///
        } else {
            // Display failed reason
            countdownElement.text('Failed: ' + failedReason); // Or handle as needed
            var subscription_Information = $('#Subscription_Information');
                            var error_message = $('#error-message');

                            countdownElement.html('<span style="color: #697a8d; font-size: 25px;">Transaction Status: </span><span style="color: #ff9999; font-size: 25px;">Failed :(</span><br><span style="color: #697a8d; font-size: 25px;">Sale Status: </span><span style="color: #ff9999; font-size: 25px;">Failed</span>');
                            ////////
                            $('#proceedBtn').prop('disabled', true);
                            var subscription_Information = $('#Subscription_Information');
                            var error_message = $('#error-message');

                            // Update the content of the span with the new information

                          subscription_Information.html('Transaction Information: ' + TransactionID + '<br>Failed Reason: <span style="color: #ff9999;">' + failedReason + '</span>');


                        // Start the countdown immediately
                        startCountdownMpin();
                            // Display error message (if any)
                            if (failedReason) {
                              error_message.text('Error: ' + failedReason);
                            } else {
                              // Clear error message if there is no error
                              error_message.text('');
                            }
        }

        },
          error: function (xhr, status, error ,errorjs) {
          console.error(xhr.responseText);
          // Handle error response from the controller

          // Stop the counter and display the error message in the modal
          stopTimer();

          // Extract the error message from the response
          var errorMessage = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Unknown error';
          var errorJs = (xhr.responseJSON && xhr.responseJSON.errorjs) ? xhr.responseJSON.errorjs : 'Unknown error';

            $('#countdown').text(errorJs);
          errorMessageElement.text('Error: ' + errorMessage);
          errorMessageElement.show();
        },
        complete: function () {

          if(resultCode === "0")
          {
          $.ajax({
                type: 'POST',
                url: '{{ route("sms-delivery-route") }}', // Replace with the actual route for SMS delivery
                data: {
                    msisdn: msisdn,
                    TransactionId : TransactionID,
                    agentId: {{ session('agent')->agent_id }},
                    referenceId: referenceId,
                    SelectedPlan :selectedPlanId,
                    amount:product_amount,
                    plantext : plantext,
                },
                success: function (data) {
                    // Handle success (optional)
                    console.log(data);
                },
                error: function () {
                    // Handle error (optional)
                    console.error('Error in SMS delivery request');
                },
            });
          }
        }
    });
}



        // Start the timer


function startTimer(duration) {
    var timer = duration, minutes, seconds;
    window.timerId = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        $('#countdown').text('M-PIN Expiration Time: ' + minutes + ":" + seconds);

        if (--timer < 0) {
            // Timer reached zero, handle accordingly
            stopTimer();
            $('#countdown').text('M-PIN Expired');
            //errorMessageElement.show();
            // You may want to cancel the AJAX request or handle it differently
            // For example, you can use clearTimeout(timerId);
        }
    }, 1000);
}

function stopTimer() {
    clearInterval(window.timerId);
}

function resetForm() {
    //stopTimer();
    // Optionally, hide any error messages or reset other elements
    $('#countdown').text('');
    $('#mobile-number').val('');

    $('#error-message').hide();
    $('#Subscription_Information').text('');
    $('#proceedBtn').prop('disabled', false);

    var mobileError = document.getElementById("mobile-error");
    mobileError.innerHTML = "";


    modernStepper.to(0);


}


function startCountdownMpin() {
    var subscription_Information = $('#Subscription_Information');
   var seconds = 20;
    var timerSpan = $('<span id="timer">' + seconds + ' seconds</span>');
    subscription_Information.append('<br>Next M-PIN Request: Timer ').append(timerSpan);

    function updateTimer() {
        seconds--;

        if (seconds < 0) {
            clearInterval(timerInterval);
            $('#proceedBtn').prop('disabled', false);
        } else {
            timerSpan.text(seconds + ' seconds');
        }
    }

    // Update the timer every second
    var timerInterval = setInterval(updateTimer, 1000);

    // Initial update
    updateTimer();
}



document.getElementById('beneficiary-mobile').addEventListener('input', function () {
        validateMobileNumberBeneficinary();
         // Update the "Next" button based on form validity
    });

    function validateMobileNumberBeneficinary() {
        var mobileNumberInput_b = document.getElementById('beneficiary-mobile');
        var mobileNumberError_E = document.getElementById('beneficiary-error');
        var mobileNumberRegex = /^\d{11}$/;

        if (mobileNumberInput_b.value.length > 11) {
          mobileNumberInput_b.value = mobileNumberInput_b.value.slice(0, 11); // Truncate to 11 characters
    }

        if (mobileNumberRegex.test(mobileNumberInput_b.value)) {
          mobileNumberError_E.textContent = '';
        } else {
          mobileNumberError_E.textContent = 'Invalid mobile number. Please enter 11 digits.';
        }
    }

    document.getElementById('mobile-number').addEventListener('input', function () {
        validateMobileNumber();
         // Update the "Next" button based on form validity
    });

function validateMobileNumber() {
        var mobileNumberInput = document.getElementById('mobile-number');
        var mobileNumberError = document.getElementById('mobile-error');
        var mobileNumberRegex = /^\d{11}$/;

        if (mobileNumberInput.value.length > 11) {
        mobileNumberInput.value = mobileNumberInput.value.slice(0, 11); // Truncate to 11 characters
    }


        if (mobileNumberRegex.test(mobileNumberInput.value)) {
            mobileNumberError.textContent = '';
        } else {
            mobileNumberError.textContent = 'Invalid mobile number. Please enter 11 digits.';
        }
	copyMobileNumber();
    }

document.getElementById('cnic').addEventListener('input', function () {
        validateCNIC();
         // Update the "Next" button based on form validity
    });

function validateCNIC()
    {
          var cnicInput = document.getElementById('cnic');
          var cnicError = document.getElementById('cnic-error');
          var cnicRegex = /^[0-9]{13}$/;

          if (cnicInput.value.length > 11) {
            cnicInput.value = cnicInput.value.slice(0, 13); // Truncate to 11 characters
      }

          if (cnicRegex.test(cnicInput.value)) {
              cnicError.textContent = '';
          } else {
              cnicError.textContent = 'Invalid CNIC format. Please use the format: 6110185205235';
          }
    }

    //Benficinary Cnic

    document.getElementById('beneficiary-cnic').addEventListener('input', function() {
        validateCNIC_B();
    });

    function validateCNIC_B() {
        var cnicInput = document.getElementById('beneficiary-cnic');
        var cnicError = document.getElementById('cnicError-Beneficinary');
        var cnicRegex = /^[0-9]{5}[0-9]{7}[0-9]{1}$/;

        if (cnicInput.value.length > 13) {
          cnicInput.value = cnicInput.value.slice(0, 13); // Truncate to 11 characters
        }
        if (cnicRegex.test(cnicInput.value)) {
            cnicError.textContent = '';
        } else {
            cnicError.textContent = 'Invalid CNIC format. Please use the format: 6110185205256';
        }
    }


  function preFillForm() {
    document.getElementById("mobile-number").value = "00000000000";
    document.getElementById("cnic").value = "0000000000000";
    document.getElementById("beneficiary-name").value = "Not Provided";
    document.getElementById("beneficiary-mobile").value = "00000000000";
    document.getElementById("beneficiary-cnic").value = "0000000000000";
  }

  // Call the function to pre-fill the form when the page loads
  window.onload = preFillForm;




</script>





@endsection()
