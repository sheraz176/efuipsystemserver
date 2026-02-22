@extends('super_agent_Interested.layout.master')
@include('superadmin.partials.style')

@section('content')
    <div class="ms-content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="ms-panel">
                    <div class="ms-panel-header">
                        <h6>Upload Claim</h6>
                    </div>

                    <a href="{{ route('claims.download.dummy.csv.two') }}" class="btn btn-secondary mb-3">
                        Download Dummy CSV
                    </a>

                    <div class="ms-panel-body">
                        <form id="claimForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label>Upload CSV File</label>
                                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                            </div>

                            <br>



                            <button type="submit" class="btn btn-primary">
                                Upload Claims
                            </button>
                        </form>

                        <hr>

                        <div id="loader" style="display:none">
                            <strong>Uploading claims...</strong>
                        </div>

                        <div id="result"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('superadmin.partials.script')


    <script>
        $('#claimForm').on('submit', function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            $('#loader').show();
            $('#result').html('');

            $.ajax({
                url: "{{ route('claims.bulk.upload') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#loader').hide();

                    let html = `
                <p><b>Total:</b> ${res.total}</p>
                <p style="color:green"><b>Success:</b> ${res.success}</p>
                <p style="color:red"><b>Failed:</b> ${res.failed}</p>
            `;

                    if (res.errors.length > 0) {
                        html += '<ul>';
                        res.errors.forEach(err => {
                            html += `<li>${err.msisdn} - ${err.reason}</li>`;
                        });
                        html += '</ul>';
                    }

                    $('#result').html(html);
                },
                error: function() {
                    $('#loader').hide();
                    alert('Something went wrong');
                }
            });
        });
    </script>
@endsection
