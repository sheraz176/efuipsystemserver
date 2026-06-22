<!DOCTYPE html>
<html>
<head>
    <title>Customer Claim Form</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border-radius: 16px;
        }

        .form-control, .form-select {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 10px;
        }

        #planMessage div {
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Customer Claim Upload Form</h5>
                </div>

                <div class="card-body">

                    <form id="claimForm">
                        @csrf

                        <div class="row g-3">

                            <!-- Name -->
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" id="agent_id" name="agent_id" class="form-control" required>
                            </div>

                            <!-- MSISDN -->
                            <div class="col-md-6">
                                <label class="form-label">MSISDN</label>
                                <input type="text" id="msisdn" name="msisdn" class="form-control" required>
                            </div>

                            <!-- Plan Message -->
                            <div class="col-12">
                                <div id="planMessage"></div>
                            </div>

                            <!-- Plan -->
                            <div class="col-md-6">
                                <label class="form-label">Select Plan</label>
                                <select id="plan_id" name="plan_id" class="form-select" required>
                                    <option value="">Select Plan</option>
                                </select>
                            </div>

                            <!-- Claim Amount -->
                            <div class="col-md-6">
                                <label class="form-label">Claim Amount</label>
                                <input type="number" name="claim_amount" class="form-control" required>
                            </div>

                            <!-- Type -->
                            <div class="col-12">
                                <label class="form-label">Claim Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="hospitalization">Hospitalization</option>
                                    <option value="medical_and_lab_expense">Medical & Lab Expense</option>
                                </select>
                            </div>

                            <!-- Files -->
                            <div class="col-md-6">
                                <label>Doctor Prescription</label>
                                <input type="file" id="doctor_prescription" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Medical Bill</label>
                                <input type="file" id="medical_bill" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Lab Bill</label>
                                <input type="file" id="lab_bill" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Other Document</label>
                                <input type="file" id="other" class="form-control">
                            </div>

                            <!-- Submit -->
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    Submit Claim
                                </button>
                            </div>

                        </div>
                    </form>

                </div>

            </div>

        </div>
    </div>

</div>

<!-- JS -->
<script>
document.getElementById('claimForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = {
        _token: "{{ csrf_token() }}",
        agent_id: document.querySelector('[name="agent_id"]').value,
        msisdn: document.querySelector('[name="msisdn"]').value,
        claim_amount: document.querySelector('[name="claim_amount"]').value,
        type: document.querySelector('[name="type"]').value,
        plan_id: document.querySelector('[name="plan_id"]').value,
    };

    let files = ['doctor_prescription', 'medical_bill', 'lab_bill', 'other'];

    let promises = files.map(field => {
        let input = document.getElementById(field);

        if (input.files.length > 0) {
            let file = input.files[0];

            return toBase64(file).then(base64 => {
                formData[field] = {
                    base64: base64,
                    ext: file.name.split('.').pop()
                };
            });
        }
    });

    Promise.all(promises).then(() => {

        fetch("{{ route('customer.claims.uplaod.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || "Submitted Successfully");
            document.getElementById('claimForm').reset();
        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong");
        });

    });
});

// Base64 convert
function toBase64(file) {
    return new Promise((resolve, reject) => {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = error => reject(error);
    });
}


// MSISDN plan fetch
document.getElementById('msisdn').addEventListener('keyup', function() {

    let msisdn = this.value;
    if (msisdn.length < 10) return;

    fetch("{{ route('msisdn.search.plans') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ msisdn: msisdn })
    })
    .then(res => res.json())
    .then(data => {

        let planSelect = document.getElementById('plan_id');
        let msg = document.getElementById('planMessage');

        planSelect.innerHTML = `<option value="">Select Plan</option>`;
        msg.innerHTML = "";

        if (data.plans && data.plans.length > 0) {

            data.plans.forEach(p => {

                planSelect.innerHTML += `
                    <option value="${p.plan_id}">
                        ${p.plan_name}
                    </option>
                `;

                msg.innerHTML += `
                    <div class="text-success fw-bold">
                        Active Plan: ${p.plan_name}
                    </div>
                `;
            });

        } else {
            msg.innerHTML = `<div class="text-danger fw-bold">No active plan found</div>`;
        }
    });

});
</script>

</body>
</html>
