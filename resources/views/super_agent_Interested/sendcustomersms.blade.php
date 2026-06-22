@extends('super_agent_Interested.layout.master')
@include('superadmin.partials.style')

@section('content')

<div class="ms-content-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="ms-panel">
                <div class="ms-panel-header">
                    <h6>Upload Customer Claim Form</h6>
                </div>

                <div class="ms-panel-body">
                    <form id="claimForm">
                        @csrf

                        <input  type="hidden"  name="agent_id"  value="{{ session('agent')->username }} ">

                        <div class="form-group">
    <label>MSISDN</label>
    <input type="text" id="msisdn" name="msisdn" class="form-control" required>
</div>

<div id="planMessage"></div>

<div class="form-group">
    <label>Select Plan <span class="text-danger">*</span></label>
    <select id="plan_id" name="plan_name" class="form-control" required>
        <option value="">Select Plan</option>
    </select>
</div>



                        <br>

                        <button type="submit" class="btn btn-primary">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('superadmin.partials.script')

<script>
document.getElementById('claimForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    let btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerText = "Sending...";

    let msisdn = document.getElementById('msisdn').value;
    let planName = document.getElementById('plan_id').value;

    // ❌ validation before request
    if (!msisdn) {
        alert("MSISDN required");
        btn.disabled = false;
        btn.innerText = "Send Message";
        return;
    }

    if (!planName) {
        alert("Please select a plan");
        btn.disabled = false;
        btn.innerText = "Send Message";
        return;
    }

    let formData = {
        msisdn: msisdn,
        plan_name: planName,
        agent_id: document.querySelector('[name="agent_id"]').value
    };

    console.log("Sending Data:", formData);

    try {
        let response = await fetch("{{ route('claims.message') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(formData)
        });

        let data = await response.json();

        console.log("Server Response:", data);

        if (response.ok && data.status) {
            alert("✅ Success: " + data.message);

            // optional reset
            document.getElementById('claimForm').reset();
            document.getElementById('planMessage').innerHTML = "";

        } else {
            alert("❌ Failed: " + (data.message || "Something went wrong"));
        }

    } catch (error) {
        console.error("Request Error:", error);
        alert("❌ Network or Server Error");
    }

    btn.disabled = false;
    btn.innerText = "Send Message";
});


// =============================
// MSISDN PLAN FETCH
// =============================
document.getElementById('msisdn').addEventListener('keyup', function () {

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
        let messageBox = document.getElementById('planMessage');

        planSelect.innerHTML = `<option value="">Select Plan</option>`;
        messageBox.innerHTML = "";

        if (data.plans && data.plans.length > 0) {

            data.plans.forEach(item => {

                planSelect.innerHTML += `
                    <option value="${item.plan_name}">
                        ${item.plan_name}
                    </option>
                `;

                messageBox.innerHTML += `
                    <div style="color: green; font-weight: 600;">
                        Subscribed: ${item.plan_name}
                    </div>
                `;
            });

        } else {
            messageBox.innerHTML = `
                <div style="color: red; font-weight: 600;">
                    No active plan found
                </div>
            `;
        }
    })
    .catch(err => {
        console.error("Plan Fetch Error:", err);
    });
});
</script>

@endsection
