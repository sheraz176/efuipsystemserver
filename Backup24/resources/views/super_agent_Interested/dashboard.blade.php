@extends('super_agent_Interested.layout.master')

@section('content')

@if(session('status'))
    <div class="alert alert-success shadow-sm">
        {{ session('status') }}
    </div>
@endif

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- WELCOME --}}
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="card-body">
                            <h4 class="fw-bold text-primary">
                                Welcome {{ session('agent')->username }} 🎉
                            </h4>
                            <p class="mb-0 text-muted">
                                Claims dashboard overview & performance insights
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('/assets/img/illustrations/man-with-laptop-light.png') }}" height="140">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row g-3 mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Claims</h6>
                        <h3>{{ number_format($totalClaims) }}</h3>
                    </div>
                    <i class="bi bi-clipboard-data fs-1 opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Approved</h6>
                        <h3>{{ number_format($approved) }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-100"></i>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Rejected</h6>
                        <h3>{{ number_format($rejected) }}</h3>
                    </div>
                    <i class="bi bi-x-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-gradient-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Pending</h6>
                        <h3>{{ number_format($pending) }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- AVG TAT --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 bg-light text-center">
                <div class="card-body py-4">
                    <h6 class="text-muted">Claim Upload to Decision TAT</h6>
                    <h1 class="fw-bold text-primary">
                        {{ number_format($avgTat,1) }}
                        <small class="fs-5">Days</small>
                    </h1>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="row g-3 mb-4">

        {{-- DONUT --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-semibold">
                    Status of Claims
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- LINE --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-semibold">
                    Claims Upload to Decision (TAT)
                </div>
                <div class="card-body">
                    <canvas id="tatChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- BAR --}}
   <div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Rejection Reasons</h5>
            </div>
            <div class="card-body">
                <canvas id="rejectionReasonChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>


</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* STATUS DONUT */
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusData->keys()) !!},
        datasets: [{
            data: {!! json_encode($statusData->values()) !!},
            backgroundColor: ['#28a745','#dc3545','#ffc107']
        }]
    },
    options: {
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

/* TAT LINE */
new Chart(document.getElementById('tatChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($tatChart->pluck('day')) !!},
        datasets: [{
            label: 'Avg Days',
            data: {!! json_encode($tatChart->pluck('avg_days')) !!},
            borderWidth: 2,
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

/* REJECTION BAR */

</script>

<script>
const rejectionCtx = document.getElementById('rejectionReasonChart');

new Chart(rejectionCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($rejectionReasons->pluck('rejection_reason')) !!},
        datasets: [{
            data: {!! json_encode($rejectionReasons->pluck('total')) !!},
            backgroundColor: [
                '#2F80ED',   // Blue
                '#9E9E9E',   // Grey
                '#EB5757'    // Red
            ],
            borderRadius: 6,
            barThickness: 24
        }]
    },
    options: {
        indexAxis: 'y',   // 🔥 Horizontal
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: '#E0E0E0' },
                ticks: { color: '#333' }
            },
            y: {
                grid: { display: false },
                ticks: { color: '#333' }
            }
        }
    }
});
</script>



@endsection
