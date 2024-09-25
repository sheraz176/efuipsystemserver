@extends('superadmin.layout.master')
@include('superadmin.partials.style')
<link href="{{asset('newdes/assets/css/toastr.min.css')}}" rel="stylesheet">

@section('content')


<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb pl-0">
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}"><i class="material-icons"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bulk IVR Subscription Process</li>
                </ol>
            </nav>

            <div class="ms-panel">
                <div class="ms-panel-header ms-panel-custome align-items-center">
                    <div class="row mb-3">
                        {{-- <label for="dateFilter">upload</label> --}}
                        <h5 class="mb-0">Subscription IVR Subscription Manager</h5>
                    </div>
                    <div class="col-md-6">

                        {{-- <label for="dateFilter">Filter by Date:</label> --}}

                    </div>

                    <div class="col-md-4 mt-6" style="margin-left: -40%">
                        <a href="{{ route('superadmin.bulk.sub.index') }}"> <button type="submit"
                                class="btn btn-primary btn-sm">IVR Subscription Logs</button>
                        </a>
                        <a href="{{ route('download.sample.csv') }}" class="btn btn-primary btn-sm">
                            Download Sample
                        </a>
                    </div>

                </div>

            </div>
        </div>
        <div class="col-xl-12 col-md-12">

            <div class="ms-card">


                <div class="ms-card-body">
                    <h6>Upload File</h6>
                    <form action="{{ route('superadmin.Subbuilkmanager.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- File Upload -->
                            <div class="form-group col-md-6">
                                <label for="file">Upload CSV File:</label>
                                <input type="file" name="file" class="form-control" required>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm ">
                                    <i class='bx bx-up-arrow-alt'></i>Upload
                                </button>
                            </div>
                        </div>
                    </form>

                    <br>
                   <hr>
                   <div id="responseMessage"></div>
                   <h6 class="text-center">Run IVR Subscription</h6>
                   <div class="ms-panel-header ms-panel-custome d-flex flex-column align-items-center">
                       <button id="processBulkRefundBtn" class="btn btn-danger btn-sm">
                        <i class='bx bx-up-arrow-alt'></i> Process IVR Subscription</button>
                       <div id="timer" class="mt-2"></div>
                   </div>
                     <br>
                    <h6>Processed MSISDN Results</h6>
                    <button id="fetchResults" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Fetch Processed Results</button>
                    <hr>
                    <div id="resultsContainer">
                </div>
            </div>

        </div>
    </div>
</div>




<script>
    function toastSuccess() {
        // alert('hi');
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.success('Upload Csv File Successfull.', 'Successfull !');
    }

    function toastdanger() {
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.error('Invalid response from API', 'Some thing Wrong !');
    }
    function toasterror() {
        toastr.remove();
        toastr.options.positionClass = "toast-top-right";
        toastr.error('Subscription Not Found', 'Some thing Wrong !');
    }

</script>
<script>
    $(document).ready(function() {

        var created = "{{ Session::get('success') }}";
        if (created) {
            toastSuccess();
        }

        var error = "{{ Session::get('error') }}";
        if (error) {
            toastdanger();
        }

        var erroring = "{{ Session::get('erroring') }}";
        if (erroring) {
            toasterror();
        }



    });

</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        let timerInterval;

        // Function to start the timer
        function startTimer() {
            let seconds = 0;
            timerInterval = setInterval(function() {
                seconds++;
                $('#timer').text('Processing time: ' + seconds + ' seconds');
            }, 1000); // Update timer every second
        }

        // Function to stop the timer
        function stopTimer() {
            clearInterval(timerInterval);
        }

        $('#processBulkRefundBtn').click(function() {
            // Start the timer when the button is clicked
            startTimer();

            $.ajax({
                url: '{{ route("process.bulk.sub") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Stop the timer and display the success message
                    stopTimer();
                    $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                },
                error: function(response) {
                    // Stop the timer and display the error message
                    stopTimer();
                    $('#responseMessage').html('<div class="alert alert-danger">' + response.responseJSON.message + '</div>');
                }
            });
        });
    });
</script>

  <script>
        document.getElementById('fetchResults').addEventListener('click', function() {
            fetchResults();
        });

        function fetchResults() {
            fetch('{{ route('getProcessedResults.sub') }}')
                .then(response => response.json())
                .then(data => {
                    let resultsContainer = document.getElementById('resultsContainer');
                    resultsContainer.innerHTML = ''; // Clear previous results

                    if (data.length > 0) {
                        let table = document.createElement('table');
                        table.className = 'table table-bordered';

                        let thead = document.createElement('thead');
                        thead.innerHTML = '<tr><th>MSISDN</th><th>Status</th></tr>';
                        table.appendChild(thead);

                        let tbody = document.createElement('tbody');

                        data.forEach(result => {
                            let row = document.createElement('tr');
                            row.innerHTML = `<td>${result.msisdn}</td><td>${result.status}</td>`;
                            tbody.appendChild(row);
                        });

                        table.appendChild(tbody);
                        resultsContainer.appendChild(table);
                    } else {
                        resultsContainer.innerHTML = '<p>No results to display.</p>';
                    }
                })
                .catch(error => console.error('Error fetching results:', error));
        }
    </script>








<script src="{{asset('newdes/assets/js/toastr.min.js')}}"> </script>
<script src="{{asset('newdes/assets/js/toast.js')}}"> </script>

@endsection
