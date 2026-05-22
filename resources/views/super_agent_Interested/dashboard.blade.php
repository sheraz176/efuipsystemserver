@extends('super_agent_Interested.layout.master')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container-xxl flex-grow-1 container-p-y">

{{-- ================= CHANNEL DROPDOWN ================= --}}

{{-- ================= WELCOME ================= --}}
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <div class="row align-items-center">

                    {{-- LEFT SIDE TEXT --}}
                    <div class="col-md-6">
                        <h4 class="fw-bold text-primary mb-1">
                            Welcome {{ session('agent')->username }}
                        </h4>
                        <p class="text-muted mb-0">
                            Claims dashboard overview
                        </p>
                    </div>

                    {{-- RIGHT SIDE DROPDOWN (now properly aligned right) --}}
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        
                        <label class="fw-bold d-block">Select Channel</label>

                        <select id="channelSelect" class="form-control d-inline-block w-auto">
                            <option value="claim" selected>Claim (Default)</option>
                            <option value="sehatplus">Sehat+</option>
                        </select>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
{{-- ================= CARDS ================= --}}
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <div class="card  text-white">
            <div class="card-body">
                <h6>Total Claims</h6>
                <h3 id="totalClaims">{{ number_format($totalClaims) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white">
            <div class="card-body">
                <h6>Approved</h6>
                <h3 id="approvedClaims">{{ number_format($approved) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white">
            <div class="card-body">
                <h6>Rejected</h6>
                <h3 id="rejectedClaims">{{ number_format($rejected) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6>Pending</h6>
                <h3 id="pendingClaims">{{ number_format($pending) }}</h3>
            </div>
        </div>
    </div>

</div>

{{-- ================= TAT ================= --}}
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card text-center bg-light">
            <div class="card-body">
                <h6>TAT (Upload to Decision)</h6>
                <h1 class="text-primary">
                    <span id="avgTat">{{ number_format($avgTat,1) }}</span> Days
                </h1>
            </div>
        </div>
    </div>
</div>

{{-- ================= CHARTS ================= --}}
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Status Chart</div>
            <div class="card-body">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">TAT Trend</div>
            <div class="card-body">
                <canvas id="tatChart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Rejection Reasons</div>
            <div class="card-body">
                <canvas id="rejectionReasonChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Channels</div>
            <div class="card-body">
                <canvas id="channelsChart"></canvas>
            </div>
        </div>
    </div>
</div>

</div>

{{-- ================= CHART INIT ================= --}}
<script>

window.statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusData->keys()) !!},
        datasets: [{
            data: {!! json_encode($statusData->values()) !!},
            backgroundColor: ['#28a745','#dc3545','#ffc107']
        }]
    }
});

window.tatChart = new Chart(document.getElementById('tatChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($tatChart->pluck('day')) !!},
        datasets: [{
            data: {!! json_encode($tatChart->pluck('avg_days')) !!}
        }]
    }
});

window.rejectionReasonChart = new Chart(document.getElementById('rejectionReasonChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($rejectionReasons->pluck('rejection_reason')) !!},
        datasets: [{
            data: {!! json_encode($rejectionReasons->pluck('total')) !!}
        }]
    },
    options: { indexAxis: 'y' }
});

window.channelsChart = new Chart(document.getElementById('channelsChart'), {
    type: 'bar',
    data: {
        labels: @json($channels->pluck('chanel_name')),
        datasets: [{
            data: @json($channels->pluck('total'))
        }]
    },
    options: { indexAxis: 'y' }
});
</script>

{{-- ================= API SWITCH ================= --}}
<script>
$('#channelSelect').on('change', function () {

    let channel = $(this).val();

    if (channel === 'claim') {
        location.reload();
        return;
    }

    $.ajax({
        url: "https://jazzcash-health.efulife.com/api/dashboard/claims-summary",
        type: "GET",
        data: {
            channel: "Sehat+"
        },

        success: function (res) {

            let d = res.data;

            /* ================= CARDS ================= */
            $('#totalClaims').text(d.total_claims ?? 0);
            $('#approvedClaims').text(d.approved ?? 0);
            $('#rejectedClaims').text(d.rejected ?? 0);
            $('#pendingClaims').text(d.pending ?? 0);
            $('#avgTat').text((d.avg_tat_days ?? 0).toFixed(2));

            /* ================= STATUS CHART ================= */
            window.statusChart.data.labels = ["Approved","Rejected","Pending"];
            window.statusChart.data.datasets[0].data = [
                d.approved,
                d.rejected,
                d.pending
            ];
            window.statusChart.update();

            /* ================= TAT CHART ================= */
            window.tatChart.data.labels = ["Avg TAT"];
            window.tatChart.data.datasets[0].data = [d.avg_tat_days];
            window.tatChart.update();

            /* ================= REJECTION ================= */
            window.rejectionReasonChart.data.labels = d.rejection_reasons.map(x => x.remarks);
            window.rejectionReasonChart.data.datasets[0].data = d.rejection_reasons.map(x => x.total);
            window.rejectionReasonChart.update();

            /* ================= CHANNELS ================= */
            window.channelsChart.data.labels = d.channels;
            window.channelsChart.data.datasets[0].data = d.channels.map(() => d.total_claims);
            window.channelsChart.update();

        },

        error: function (err) {
            console.log(err.responseText);
            alert("Sehat+ API Error");
        }
    });

});
</script>

@endsection