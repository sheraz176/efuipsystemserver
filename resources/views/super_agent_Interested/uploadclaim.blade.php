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

                    <div class="ms-panel-body">
                        <form id="claimForm">
                            @csrf

                            <input type="hidden" name="agent_id" value="{{ session('agent')->username }} ">

                            <div class="form-group">
                                <label>MSISDN</label>
                                <input type="text" name="msisdn" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Claim Amount</label>
                                <input type="number" name="claim_amount" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Claim Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="hospitalization">Hospitalization</option>
                                    <option value="medical_and_lab_expense">Medical & Lab Expense</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Doctor Prescription</label>
                                <input type="file" id="doctor_prescription" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Medical Bill</label>
                                <input type="file" id="medical_bill" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Lab Bill</label>
                                <input type="file" id="lab_bill" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Other Document</label>
                                <input type="file" id="other" class="form-control">
                            </div>

                            <br>

                            <button type="submit" class="btn btn-primary">
                                Submit Claim
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('superadmin.partials.script')

    <script>
        document.getElementById('claimForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let formData = {
                _token: "{{ csrf_token() }}",
                msisdn: document.querySelector('[name="msisdn"]').value,
                claim_amount: document.querySelector('[name="claim_amount"]').value,
                type: document.querySelector('[name="type"]').value,
                agent_id: document.querySelector('[name="agent_id"]').value,
            };

            const files = ['doctor_prescription', 'medical_bill', 'lab_bill', 'other'];

            let promises = files.map(field => {
                let fileInput = document.getElementById(field);
                if (fileInput.files.length > 0) {
                    let file = fileInput.files[0];
                    return toBase64(file).then(base64 => {
                        formData[field] = {
                            base64: base64,
                            type: file.name.split('.').pop()
                        };
                    });
                }
            });

            Promise.all(promises).then(() => {
                fetch("{{ route('claims.upload') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Something went wrong');
                    });
            });
        });

        function toBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result.split(',')[1]);
                reader.onerror = error => reject(error);
            });
        }
    </script>
@endsection
