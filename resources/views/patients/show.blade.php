@extends('layouts.app')

@section('title', 'Patient Details - Ovum Doctor')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.css" rel="stylesheet">
@endsection

@section('content')
<!-- Patient Header -->
<div class="patient-header p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="mb-2">{{ $patient->name }}</h2>
            <div class="d-flex gap-4 text-muted">
                <span><i class="fas fa-calendar-days me-2"></i>{{ $patient->age }} years old (DOB: {{ $patient->date_of_birth->format('Y-m-d') }})</span>
                @if($patient->medical_condition)
                <span class="text-danger"><i class="fas fa-circle-exclamation me-2"></i>{{ $patient->medical_condition }}</span>
                @endif
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newVisitModal">Add New Visit</button>
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-primary">Edit Patient</a>
        </div>
    </div>
</div>

<!-- Main Content -->
<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">Overview</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#vitals">Vitals History</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#visits">Past Visits</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medications">Medications</button>
    </li>
</ul>

<div class="tab-content">
    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="overview">
        <!-- Vital Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="vital-card card">
                    <div class="card-body">
                        <div class="vital-icon bg-soft-danger text-danger">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h6 class="text-muted mb-2">Blood Pressure</h6>
                        <h2 class="mb-0">{{ $patient->latest_vitals->blood_pressure ?? 'N/A' }}</h2>
                        <small class="text-muted">Last checked: {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="vital-card card">
                    <div class="card-body">
                        <div class="vital-icon bg-soft-primary text-primary">
                            <i class="fas fa-wave-square"></i>
                        </div>
                        <h6 class="text-muted mb-2">Heart Rate</h6>
                        <h2 class="mb-0">{{ $patient->latest_vitals->heart_rate ?? 'N/A' }} bpm</h2>
                        <small class="text-muted">Last checked: {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="vital-card card">
                    <div class="card-body">
                        <div class="vital-icon bg-soft-warning text-warning">
                            <i class="fas fa-temperature-high"></i>
                        </div>
                        <h6 class="text-muted mb-2">Temperature</h6>
                        <h2 class="mb-0">{{ $patient->latest_vitals->temperature ?? 'N/A' }}°C</h2>
                        <small class="text-muted">Last checked: {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="vital-card card">
                    <div class="card-body">
                        <div class="vital-icon bg-soft-success text-success">
                            <i class="fas fa-weight-scale"></i>
                        </div>
                        <h6 class="text-muted mb-2">Weight</h6>
                        <h2 class="mb-0">{{ $patient->latest_vitals->weight ?? 'N/A' }} kg</h2>
                        <small class="text-muted">Last checked: {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Cycle Variations</h5>
                <div class="chart-container">
                    <div id="chart-container" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-clock me-2"></i>Recent Visits
                        </h5>
                        @forelse($patient->recent_visits as $visit)
                        <div class="visit-card p-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $visit->type }}</h6>
                                    <small class="text-muted">Dr. {{ $visit->doctor->name }}</small>
                                </div>
                                <small class="text-muted">{{ $visit->visited_at->format('Y-m-d') }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No recent visits</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-pills me-2"></i>Current Medications
                        </h5>
                        @forelse($patient->current_medications as $medication)
                        <div class="visit-card p-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-1">{{ $medication->name }}</h6>
                                    <small class="text-muted">{{ $medication->dosage }}</small>
                                </div>
                                <span class="status-badge bg-{{ $medication->status_color }} bg-opacity-10 text-{{ $medication->status_color }}">
                                    {{ $medication->status }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No current medications</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vitals History Tab -->
    <div class="tab-pane fade" id="vitals" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Vitals History</h5>
                <div id="vitals-chart" style="height: 200px;"></div>
            </div>
        </div>
    </div>

    <!-- Past Visits Tab -->
    <div class="tab-pane fade" id="visits" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Past Visits</h5>
                <ul class="list-group">
                    @forelse($patient->visits as $visit)
                    <li class="list-group-item">
                        {{ $visit->type }} with Dr. {{ $visit->doctor->name }} - {{ $visit->visited_at->format('Y-m-d') }}
                    </li>
                    @empty
                    <li class="list-group-item">No past visits recorded</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Medications Tab -->
    <div class="tab-pane fade" id="medications" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Medications</h5>
                <ul class="list-group">
                    @forelse($patient->medications as $medication)
                    <li class="list-group-item">{{ $medication->name }} - {{ $medication->dosage }}</li>
                    @empty
                    <li class="list-group-item">No medications recorded</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- New Visit Modal -->
@include('patients.partials.new-visit-modal')
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Vitals Chart
    const vitalsChart = new Chart(document.getElementById('vitals-chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($vitalsHistory->pluck('date')),
            datasets: [{
                label: 'Heart Rate',
                data: @json($vitalsHistory->pluck('heart_rate')),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                tension: 0.4
            }, {
                label: 'Weight',
                data: @json($vitalsHistory->pluck('weight')),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

    // Initialize Cycle Chart
    Highcharts.chart('chart-container', {
        title: { text: 'Change in Cycle Lengths' },
        xAxis: {
            categories: @json($cycleHistory->pluck('month')),
            title: { text: 'Month' }
        },
        yAxis: {
            title: { text: 'Days' }
        },
        series: [{
            name: 'Cycle Length',
            data: @json($cycleHistory->pluck('cycle_length')),
            color: '#007bff'
        }, {
            name: 'Period Length',
            data: @json($cycleHistory->pluck('period_length')),
            color: '#28a745'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 600
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
    });
});

// we shall implemet the calling of cycle history data by this function
// This makes an Ajax request to get cycle history data
function loadCycleHistory(patientId) {

$.ajax({

    url: `/patients/${patientId}/cycle-history`,

    method: 'GET',

    success: function(data) {

        // Update the chart with the data

        updateCycleChart(data);

    }

});
// Call this when page loads

document.addEventListener('DOMContentLoaded', function() {

loadCycleHistory({{ $patient->id }});

});
}
</script>
@endsection 