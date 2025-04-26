@extends('layouts.app')

@section('title', 'Lab Results - Ovum Doctor')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <ul class="nav nav-pills" id="viewTabs">
                <li class="nav-item">
                    <a class="nav-link active" id="visual-tab" data-bs-toggle="tab" href="#visual">
                        <i class="fas fa-chart-line me-1"></i> Visual View
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tabular-tab" data-bs-toggle="tab" href="#tabular">
                        <i class="fas fa-table me-1"></i> Table View
                    </a>
                </li>
            </ul>
            <div>
                <button id="printLabResults" class="btn btn-sm btn-outline-success me-1">
                    <i class="fas fa-print me-1"></i> Print
                </button>
                <a href="{{ route('visits.labs.export-pdf', [$visit, $lab]) }}" class="btn btn-sm btn-outline-danger me-1">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
                <a href="{{ route('patients.labs.export-csv', $visit->patient) }}" class="btn btn-sm btn-outline-primary me-1">
                    <i class="fas fa-file-csv me-1"></i> Export All as CSV
                </a>
                <a href="{{ route('visits.labs.edit', [$visit, $lab]) }}" class="btn btn-sm btn-primary me-1">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <form action="{{ route('visits.labs.destroy', [$visit, $lab]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <!-- Patient Info -->
            <div class="patient-card bg-light p-3 rounded-3 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">{{ $visit->patient->name }}</h5>
                            <div class="d-flex flex-wrap gap-3 text-muted small">
                                <span><i class="fas fa-birthday-cake me-1"></i> {{ $visit->patient->date_of_birth->format('Y-m-d') }}</span>
                                <span><i class="fas fa-user-md me-1"></i> Dr. {{ $visit->doctor->name }}</span>
                                <span><i class="fas fa-clock me-1"></i> Last updated: {{ $lab->updated_at->format('M j, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('patients.labs.export-pdf', $visit->patient) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i> Export All Labs as PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content mt-4">
                <!-- Visual View -->
                <div class="tab-pane fade show active" id="visual">
                    <div class="row g-4">
                        <!-- Vital Signs Section -->
                        <div class="col-12">
                            <h4 class="text-primary mb-4"><i class="fas fa-heartbeat me-2"></i> Vital Signs</h4>
                            <div class="row g-4">
                                <!-- Respiratory Rate -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-lungs me-2 text-info"></i> Respiratory</h5>
                                                <span class="badge bg-success">Normal</span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ $lab->respiratory_rate }}</span>
                                                <small class="text-muted ms-2">brpm</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar bg-success" 
                                                         role="progressbar" 
                                                         style="width: {{ (($lab->respiratory_rate - 12) / 8) * 100 }}%"
                                                         aria-valuenow="{{ $lab->respiratory_rate }}" 
                                                         aria-valuemin="12" 
                                                         aria-valuemax="20"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>12</span>
                                                    <span>20</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Blood Pressure -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-heart-pulse me-2 text-danger"></i> Blood Pressure</h5>
                                                <span class="badge bg-{{ $lab->bp_systolic > 120 ? 'warning' : 'success' }}">{{ $lab->bp_systolic > 120 ? 'High' : 'Normal' }}</span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ $lab->bp_systolic }}</span>
                                                <small class="text-muted ms-2">/{{ $lab->bp_diastolic }} mmHg</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="bp-visual">
                                                    <div class="bp-bar systolic" style="width: {{ ($lab->bp_systolic / 200) * 100 }}%"></div>
                                                    <div class="bp-bar diastolic" style="width: {{ ($lab->bp_diastolic / 120) * 100 }}%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>Normal Range</span>
                                                    <span>90-120/60-80</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hemoglobin -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-tint me-2 text-danger"></i> Hemoglobin</h5>
                                                <span class="badge bg-{{ ($lab->hemoglobin < 12 || $lab->hemoglobin > 16) ? 'warning' : 'success' }}">
                                                    {{ ($lab->hemoglobin < 12) ? 'Low' : (($lab->hemoglobin > 16) ? 'High' : 'Normal') }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->hemoglobin, 1) }}</span>
                                                <small class="text-muted ms-2">g/dL</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ ($lab->hemoglobin < 12) ? 'bg-warning' : (($lab->hemoglobin > 16) ? 'bg-warning' : 'bg-success') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ (($lab->hemoglobin - 8) / 10) * 100 }}%"
                                                         aria-valuenow="{{ $lab->hemoglobin }}" 
                                                         aria-valuemin="8" 
                                                         aria-valuemax="18"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>8</span>
                                                    <span>18</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Waist-Hip Ratio -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-ruler me-2 text-success"></i> Waist-Hip Ratio</h5>
                                                <span class="badge bg-{{ $lab->waist_hip_ratio > 0.85 ? 'warning' : 'success' }}">
                                                    {{ $lab->waist_hip_ratio > 0.85 ? 'Above Target' : 'Optimal' }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->waist_hip_ratio, 2) }}</span>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ $lab->waist_hip_ratio > 0.85 ? 'bg-warning' : 'bg-success' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ (($lab->waist_hip_ratio - 0.5) / 1.5) * 100 }}%"
                                                         aria-valuenow="{{ $lab->waist_hip_ratio }}" 
                                                         aria-valuemin="0.5" 
                                                         aria-valuemax="2.0"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0.5</span>
                                                    <span>2.0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hormone Tests Section -->
                        <div class="col-12 mt-5">
                            <h4 class="text-primary mb-4"><i class="fas fa-vial me-2"></i> Hormone Tests</h4>
                            <div class="row g-4">
                                <!-- FSH -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i> FSH</h5>
                                                <span class="badge bg-info">Follicular</span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->fsh, 1) }}</span>
                                                <small class="text-muted ms-2">mIU/mL</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar bg-info" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->fsh / 15) * 100) }}%"
                                                         aria-valuenow="{{ $lab->fsh }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="15"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>15 mIU/mL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- LH -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2 text-warning"></i> LH</h5>
                                                <span class="badge bg-info">Follicular</span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->lh, 1) }}</span>
                                                <small class="text-muted ms-2">mIU/mL</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar bg-warning" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->lh / 20) * 100) }}%"
                                                         aria-valuenow="{{ $lab->lh }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="20"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>20 mIU/mL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FSH/LH Ratio -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-balance-scale me-2 text-success"></i> FSH/LH Ratio</h5>
                                                <span class="badge bg-{{ $lab->fsh_lh_ratio > 3 ? 'warning' : 'success' }}">
                                                    {{ $lab->fsh_lh_ratio > 3 ? 'Elevated' : 'Normal' }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->fsh_lh_ratio, 2) }}</span>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ $lab->fsh_lh_ratio > 3 ? 'bg-warning' : 'bg-success' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->fsh_lh_ratio / 5) * 100) }}%"
                                                         aria-valuenow="{{ $lab->fsh_lh_ratio }}" 
                                                         aria-valuemin="0.1" 
                                                         aria-valuemax="5"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0.1</span>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- AMH -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-egg me-2 text-info"></i> AMH</h5>
                                                <span class="badge bg-{{ $lab->amh < 1.0 ? 'warning' : 'success' }}">
                                                    {{ $lab->amh < 1.0 ? 'Low' : 'Normal' }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->amh, 2) }}</span>
                                                <small class="text-muted ms-2">ng/mL</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ $lab->amh < 1.0 ? 'bg-warning' : 'bg-success' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->amh / 5) * 100) }}%"
                                                         aria-valuenow="{{ $lab->amh }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="5"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>5 ng/mL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pregnancy Indicators Section -->
                        <div class="col-12 mt-5">
                            <h4 class="text-primary mb-4"><i class="fas fa-baby me-2"></i> Pregnancy Indicators</h4>
                            <div class="row g-4">
                                <!-- HCG Initial -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-heartbeat me-2 text-danger"></i> HCG Initial</h5>
                                                <span class="badge bg-{{ $lab->hcg_initial > 5 ? 'info' : 'secondary' }}">
                                                    {{ $lab->hcg_initial > 5 ? 'Positive' : 'Negative' }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->hcg_initial, 1) }}</span>
                                                <small class="text-muted ms-2">mIU/mL</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ $lab->hcg_initial > 5 ? 'bg-info' : 'bg-secondary' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->hcg_initial / 100) * 100) }}%"
                                                         aria-valuenow="{{ $lab->hcg_initial }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>100 mIU/mL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- HCG Follow-up -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-heartbeat me-2 text-danger"></i> HCG Follow-up</h5>
                                                @if($lab->hcg_followup)
                                                    <span class="badge bg-{{ $lab->hcg_followup > $lab->hcg_initial ? 'success' : 'danger' }}">
                                                        {{ $lab->hcg_followup > $lab->hcg_initial ? 'Increasing' : 'Decreasing' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Not Recorded</span>
                                                @endif
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ $lab->hcg_followup ? number_format($lab->hcg_followup, 1) : '—' }}</span>
                                                <small class="text-muted ms-2">mIU/mL</small>
                                            </div>
                                            <div class="mt-4">
                                                @if($lab->hcg_followup)
                                                    <div class="progress" style="height: 8px">
                                                        <div class="progress-bar {{ $lab->hcg_followup > $lab->hcg_initial ? 'bg-success' : 'bg-danger' }}" 
                                                            role="progressbar" 
                                                            style="width: {{ min(100, ($lab->hcg_followup / 100) * 100) }}%"
                                                            aria-valuenow="{{ $lab->hcg_followup }}" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2 small text-muted">
                                                        <span>0</span>
                                                        <span>100 mIU/mL</span>
                                                    </div>
                                                @else
                                                    <div class="text-center text-muted mt-3">
                                                        <em>Follow-up test not recorded</em>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progesterone -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-venus me-2 text-pink"></i> Progesterone</h5>
                                                <span class="badge bg-{{ $lab->progesterone > 10 ? 'success' : 'info' }}">
                                                    {{ $lab->progesterone > 10 ? 'Luteal Phase' : 'Follicular Phase' }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->progesterone, 1) }}</span>
                                                <small class="text-muted ms-2">ng/mL</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar bg-{{ $lab->progesterone > 10 ? 'success' : 'info' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->progesterone / 20) * 100) }}%"
                                                         aria-valuenow="{{ $lab->progesterone }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="20"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>20 ng/mL</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ultrasound Findings Section -->
                        <div class="col-12 mt-5">
                            <h4 class="text-primary mb-4"><i class="fas fa-wave-square me-2"></i> Ultrasound Findings</h4>
                            <div class="row g-4">
                                <!-- Total Follicles -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-circle me-2 text-warning"></i> Total Follicles</h5>
                                                <span class="badge bg-{{ $lab->total_follicles > 12 ? 'warning' : 'success' }}">
                                                    {{ $lab->total_follicles > 12 ? 'PCOS Indicator' : 'Normal' }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ $lab->total_follicles }}</span>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ $lab->total_follicles > 12 ? 'bg-warning' : 'bg-success' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->total_follicles / 20) * 100) }}%"
                                                         aria-valuenow="{{ $lab->total_follicles }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="20"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>20</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Avg. Fallopian Size -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-ruler-horizontal me-2 text-info"></i> Fallopian Size</h5>
                                                <span class="badge bg-info">Measurement</span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->avg_fallopian_size, 1) }}</span>
                                                <small class="text-muted ms-2">mm</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar bg-info" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->avg_fallopian_size / 10) * 100) }}%"
                                                         aria-valuenow="{{ $lab->avg_fallopian_size }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="10"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>10 mm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Endometrium Thickness -->
                                <div class="col-xl-4 col-md-6">
                                    <div class="card metric-card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="fas fa-ruler-vertical me-2 text-success"></i> Endometrium</h5>
                                                <span class="badge bg-{{ $lab->endometrium < 7 ? 'info' : ($lab->endometrium > 12 ? 'warning' : 'success') }}">
                                                    {{ $lab->endometrium < 7 ? 'Early Follicular' : ($lab->endometrium > 12 ? 'Luteal/Check' : 'Mid-Cycle') }}
                                                </span>
                                            </div>
                                            <div class="metric-value">
                                                <span class="display-4 fw-bold">{{ number_format($lab->endometrium, 1) }}</span>
                                                <small class="text-muted ms-2">mm</small>
                                            </div>
                                            <div class="mt-4">
                                                <div class="progress" style="height: 8px">
                                                    <div class="progress-bar {{ $lab->endometrium < 7 ? 'bg-info' : ($lab->endometrium > 12 ? 'bg-warning' : 'bg-success') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min(100, ($lab->endometrium / 15) * 100) }}%"
                                                         aria-valuenow="{{ $lab->endometrium }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="15"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2 small text-muted">
                                                    <span>0</span>
                                                    <span>15 mm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table View -->
                <div class="tab-pane fade" id="tabular">
                    <!-- Original Table Layout -->
                    <div class="row">
                        <!-- Basic Measurements -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-ruler me-2"></i> Basic Measurements</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Respiratory Rate:</th>
                                            <td>{{ $lab->respiratory_rate }} breaths/min</td>
                                        </tr>
                                        <tr>
                                            <th>Hemoglobin:</th>
                                            <td>{{ $lab->hemoglobin }} g/dL</td>
                                        </tr>
                                        <tr>
                                            <th>Blood Pressure:</th>
                                            <td>{{ $lab->bp_systolic }}/{{ $lab->bp_diastolic }} mmHg</td>
                                        </tr>
                                        <tr>
                                            <th>Waist-Hip Ratio:</th>
                                            <td>{{ $lab->waist_hip_ratio }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Hormone Tests -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-vial me-2"></i> Hormone Tests</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th>FSH:</th>
                                            <td>{{ $lab->fsh }} mIU/mL</td>
                                        </tr>
                                        <tr>
                                            <th>LH:</th>
                                            <td>{{ $lab->lh }} mIU/mL</td>
                                        </tr>
                                        <tr>
                                            <th>FSH/LH Ratio:</th>
                                            <td>{{ $lab->fsh_lh_ratio }}</td>
                                        </tr>
                                        <tr>
                                            <th>AMH:</th>
                                            <td>{{ $lab->amh }} ng/mL</td>
                                        </tr>
                                        <tr>
                                            <th>TSH:</th>
                                            <td>{{ $lab->tsh }} mIU/L</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Pregnancy Indicators -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-baby me-2"></i> Pregnancy Indicators</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th>HCG Initial:</th>
                                            <td>{{ $lab->hcg_initial }} mIU/mL</td>
                                        </tr>
                                        <tr>
                                            <th>HCG Follow-up:</th>
                                            <td>{{ $lab->hcg_followup ?? 'N/A' }} mIU/mL</td>
                                        </tr>
                                        <tr>
                                            <th>Progesterone:</th>
                                            <td>{{ $lab->progesterone }} ng/mL</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Additional Tests -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-flask me-2"></i> Additional Tests</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Prolactin:</th>
                                            <td>{{ $lab->prolactin }} ng/mL</td>
                                        </tr>
                                        <tr>
                                            <th>Vitamin D3:</th>
                                            <td>{{ $lab->vitamin_d3 }} ng/mL</td>
                                        </tr>
                                        <tr>
                                            <th>Random Blood Sugar:</th>
                                            <td>{{ $lab->rbs }} mg/dL</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Ultrasound Findings -->
                        <div class="col-md-6 mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-wave-square me-2"></i> Ultrasound Findings</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Total Follicles:</th>
                                            <td>{{ $lab->total_follicles }}</td>
                                        </tr>
                                        <tr>
                                            <th>Avg. Fallopian Size:</th>
                                            <td>{{ $lab->avg_fallopian_size }} mm</td>
                                        </tr>
                                        <tr>
                                            <th>Endometrium Thickness:</th>
                                            <td>{{ $lab->endometrium }} mm</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 border-top pt-3">
                <div class="d-flex gap-2">
                    <a href="{{ route('patients.show', $visit->patient) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Patient
                    </a>
                    <button class="btn btn-outline-success print-labs-btn">
                        <i class="fas fa-print me-1"></i> Print Lab Results
                    </button>
                    <a href="{{ route('visits.labs.edit', [$visit, $lab]) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Lab Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.metric-card {
    border: 1px solid rgba(0,0,0,0.08);
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 12px;
    overflow: hidden;
}

.metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
}

.metric-value {
    margin: 1rem 0;
}

.bp-visual {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    position: relative;
}

.bp-bar {
    position: absolute;
    height: 100%;
    border-radius: 4px;
}

.bp-bar.systolic {
    background: #dc3545;
    width: 60%;
}

.bp-bar.diastolic {
    background: #0d6efd;
    width: 40%;
    left: 60%;
}

.progress-bar {
    transition: width 0.6s ease;
}

.nav-pills .nav-link {
    border-radius: 20px;
    padding: 0.5rem 1.25rem;
}

.patient-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid rgba(0,0,0,0.05);
    border-radius: 12px;
}

.metric-card .card-title {
    font-size: 1rem;
    font-weight: 500;
}

.metric-card .display-4 {
    font-size: 2.5rem;
    font-weight: 600;
}

/* Different colored stats for metrics */
.text-pink {
    color: #e83e8c;
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .metric-card .display-4 {
        font-size: 2rem;
    }
    
    .metric-card .card-title {
        font-size: 0.9rem;
    }
}

/* Add some animation to the progress bars */
.progress-bar {
    animation: progressAnimation 1s ease-in-out;
}

@keyframes progressAnimation {
    from {
        width: 0;
    }
}

/* Table styling for the tabular view */
.table-bordered td, .table-bordered th {
    border-color: rgba(0,0,0,0.1);
}

.table th {
    font-weight: 600;
    width: 40%;
}

/* Tab styling */
.nav-pills .nav-link.active {
    background-color: #0d6efd;
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
}

.tab-content {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>

<script>
// Simple tab persistence
document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence code
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', e => {
            localStorage.setItem('activeLabTab', e.target.getAttribute('href'));
        });
    });

    const activeTab = localStorage.getItem('activeLabTab');
    if (activeTab) {
        const tabTrigger = new bootstrap.Tab(document.querySelector(`[href="${activeTab}"]`));
        tabTrigger.show();
    }
    
    // Print functionality - main print button
    const printButton = document.getElementById('printLabResults');
    if (printButton) {
        printButton.addEventListener('click', printLabResults);
    }
    
    // Print functionality - tabular view print button
    const printLabsBtns = document.querySelectorAll('.print-labs-btn');
    printLabsBtns.forEach(btn => {
        btn.addEventListener('click', printLabResults);
    });
    
    function printLabResults() {
        const printContents = document.querySelector('.card-body').innerHTML;
        const originalContents = document.body.innerHTML;
        
        // Create a print-friendly version
        const printStyles = `
            <style>
                @media print {
                    body { font-family: Arial, sans-serif; }
                    .metric-card { break-inside: avoid; page-break-inside: avoid; border: 1px solid #ddd; margin-bottom: 15px; padding: 10px; }
                    .no-print { display: none !important; }
                    .patient-card { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; }
                    .card-title { font-weight: bold; margin-bottom: 10px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    .progress, .bp-visual { display: none; }
                    h4 { page-break-after: avoid; margin-top: 20px; color: #0d6efd; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
                    button, .btn, form { display: none !important; }
                    .nav-pills, .tab-pane:not(.active) { display: none !important; }
                    .tab-content > .tab-pane { display: block !important; opacity: 1 !important; }
                }
            </style>
        `;
        
        // Create header with lab info
        const patientInfo = document.querySelector('.patient-card').cloneNode(true);
        const buttons = patientInfo.querySelectorAll('.btn');
        buttons.forEach(btn => btn.remove());
        
        const labTitle = `
            <div style="text-align: center; margin-bottom: 20px;">
                <h2>Lab Results Report</h2>
                <p>Generated on ${new Date().toLocaleString()}</p>
                <p>Patient: ${document.querySelector('.patient-card h5').textContent}</p>
            </div>
        `;
        
        // Combine all into a print document
        document.body.innerHTML = printStyles + labTitle + patientInfo.outerHTML + printContents;
        
        // Print
        window.print();
        
        // Restore original content
        document.body.innerHTML = originalContents;
        
        // Reload page to properly restore JS functionality
        location.reload();
    }
});
</script>
@endsection