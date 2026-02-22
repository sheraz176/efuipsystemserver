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
                        <li class="breadcrumb-item active" aria-current="page"><i class='bx bx-down-arrow-alt'></i>Process Bulk File</li>
                    </ol>
                </nav>
                <div class="ms-panel">

                    <div class="ms-panel-header ms-panel-custome align-items-center">
                        <button id="processBulkRefundBtn" class="btn btn-danger btn-sm">Process Bulk Refund</button>
                        <div id="timer"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-md-12">

                <div class="ms-card">
                    <br>
                    <div id="responseMessage"></div>
                    <div class="ms-card-body">
                        <h6>Processed MSISDN Results</h6>
                        <button id="fetchResults" class="btn btn-primary btn-sm"><i class='bx bx-down-arrow-alt'></i>Fetch Processed Results</button>
                        <hr>
                        <div id="resultsContainer">
                    </div>
                </div>

            </div>
        </div>
    </div>


@include('superadmin.partials.script')

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
                url: '{{ route("process.bulk.refund") }}',
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
            fetch('{{ route('getProcessedResults') }}')
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




 @endsection
