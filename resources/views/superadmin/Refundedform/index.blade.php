@extends('superadmin.layout.master')
@include('superadmin.partials.style')
@section('content')
    <div class="ms-content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb pl-0">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Refunds Forms</li>
                    </ol>
                </nav>

                <div class="ms-panel">
                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <div class="row mb-3">
                            {{-- <label for="dateFilter">upload</label> --}}

                        </div>

                            <div class="col-md-6">
                                <label for="msisdn">Search by Mobile Number:</label>
                                <input type="text" id="msisdn" class="form-control" placeholder="Enter MSISDN">
                            </div>
                            <div class="col-md-2 mt-8" style="margin-top: 2%; margin-left: -20%">
                                <button type="button" id="search-button" class="btn btn-primary btn-sm">
                                    <i class='bx bx-down-arrow-alt'></i>Search
                                </button>
                            </div>


                        <div class="col-md-4 mt-6" style="margin-left: -40%">


                        </div>
                    </div>

                </div>

            </div>
            <div class="col-xl-12 col-md-12">
                <div class="ms-card">
                     <!-- Body Content Wrapper -->

        <div class="ms-content-wrapper">
            <div class="row">
                <div class="col-md-12">
                      <div id="refund-success" class="alert alert-success" style="display: none;"></div>

                </div>

                <div id="customer-info" class="col-xl-12 col-md-12">
                    <!-- Customer Information will be populated here via AJAX -->
                </div>

            </div>
        </div>
                </div>

            </div>
        </div>
    </div>




@include('superadmin.partials.script')


<script>
    $(document).ready(function() {
        // Search for customer by MSISDN
        $('#search-button').on('click', function() {
            var msisdn = $('#msisdn').val();

            $.ajax({
                url: '{{ route('superadmin.refunded.customer.search') }}',
                type: 'GET',
                data: { msisdn: msisdn },
                success: function(response) {
                    $('#customer-info').html(response).show(); // Ensure customer info is shown after search
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        $('#customer-info').html('<div class="alert alert-danger">Customer not found</div>');
                    } else {
                        console.error('Error:', xhr);
                    }
                }
            });
        });



    // Handle Refund button click
    $(document).on('click', '#refund-button', function() {
        // Get subscription_id and reason of refund
        var subscriptionId = $('#subscription_id').val();
        var reason = $('#reason').val();
        var refundErrorDiv = $('#refund-error');  // Ensure this ID matches the Blade template
        var refundSuccessDiv = $('#refund-success');  // Ensure this ID matches the Blade template

        // Clear previous messages
        refundErrorDiv.hide().html('');
        refundSuccessDiv.hide().html('');

        if (!reason.trim()) {
            refundErrorDiv.html('Please provide a reason for the refund.').show();
            return;
        }

        $.ajax({
            url: '{{ route('superadmin.refund.process') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                subscription_id: subscriptionId,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    refundSuccessDiv.html('Refund processed successfully.').show(); // Show success message

                    // Hide the customer-info div after refund success
                    $('#customer-info').hide();

                    // Optionally, clear the form fields or update the UI
                    $('#reason').val(''); // Clear the reason field
                } else {
                    refundErrorDiv.html('Refund failed: ' + response.resultDesc).show();
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error Response:', xhr.responseText); // Log the full error response for debugging

                if (xhr.responseJSON) {
                    var response = xhr.responseJSON; // Parse JSON response
                    refundErrorDiv.html('Refund failed: ' + response.resultDesc).show(); // Show error message in the div
                } else {
                    refundErrorDiv.html('An unexpected error occurred.').show();
                }
            }
        });
    });
});


    </script>



 @endsection
