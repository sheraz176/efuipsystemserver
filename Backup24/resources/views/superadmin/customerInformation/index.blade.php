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
                        <li class="breadcrumb-item active" aria-current="page">Customer Information Form</li>
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
                            <button type="button" id="search-button" class="btn btn-danger btn-sm">
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
        $('#search-button').on('click', function() {
            var msisdn = $('#msisdn').val();

            $.ajax({
                url: '{{ route('superadmin.customerinformation.search') }}',
                type: 'GET',
                data: { msisdn: msisdn },
                success: function(response) {
                    $('#customer-info').html(response);
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
    });
</script>


 @endsection
