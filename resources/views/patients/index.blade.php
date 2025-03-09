@extends('layouts.app')

@section('title', 'Patients - Ovum Doctor')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-users me-2"></i>Patients</h2>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Patient
        </a>
    </div>

    <!-- Filters/Search Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('patients.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search by name or medical condition" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="blood_type">
                        <option value="">All Blood Types</option>
                        <option value="A+" {{ request('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ request('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ request('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ request('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="AB+" {{ request('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ request('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                        <option value="O+" {{ request('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ request('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="sort">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="date_of_birth" {{ request('sort') == 'date_of_birth' ? 'selected' : '' }}>Sort by Age</option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Sort by Registration Date</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Patients List Section -->
    <div class="card shadow">
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Patient Name</th>
                                <th>Age</th>
                                <th>Contact</th>
                                <th>Blood Type</th>
                                <th>Latest Vitals</th>
                                <th>Medical Condition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle text-bg-secondary-custom me-2">
                                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $patient->name }}</h6>
                                                <small class="text-muted">{{ $patient->date_of_birth->format('Y-m-d') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $patient->date_of_birth->age }} years</td>
                                    <td>
                                        <div><i class="fas fa-phone-alt fa-sm text-primary-custom me-1"></i>{{ $patient->phone }}</div>
                                        @if($patient->email)
                                            <div><i class="fas fa-envelope fa-sm text-primary-custom me-1"></i>{{ $patient->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($patient->blood_type)
                                            <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                                        @else
                                            <span class="text-muted">Not recorded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($patient->latest_vitals)
                                            <div class="small">
                                                <div><span class="text-muted">BP:</span> {{ $patient->latest_vitals->blood_pressure ?? 'N/A' }}</div>
                                                <div><span class="text-muted">HR:</span> {{ $patient->latest_vitals->heart_rate ?? 'N/A' }} bpm</div>
                                                <div><span class="text-muted">Temp:</span> {{ $patient->latest_vitals->temperature ?? 'N/A' }}°C</div>
                                                <div class="text-muted small">{{ $patient->latest_vitals->recorded_at->diffForHumans() }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">No vitals recorded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($patient->medical_condition)
                                            <span class="badge bg-warning text-dark">{{ Str::limit($patient->medical_condition, 30) }}</span>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('patients.show', $patient) }}">
                                                        <i class="fas fa-eye me-2 text-primary"></i>View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('patients.edit', $patient) }}">
                                                        <i class="fas fa-edit me-2 text-success"></i>Edit Patient
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#newAppointmentModal" 
                                                            data-patient-id="{{ $patient->id }}" data-patient-name="{{ $patient->name }}">
                                                        <i class="fas fa-calendar-plus me-2 text-info"></i>Schedule Appointment
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#newVisitModal"
                                                            data-patient-id="{{ $patient->id }}" data-patient-name="{{ $patient->name }}">
                                                        <i class="fas fa-procedures me-2 text-warning"></i>Record Visit
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash-alt me-2"></i>Delete Patient
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $patients->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="{{ asset('assets/images/no-data.svg') }}" alt="No patients" style="max-width: 200px; opacity: 0.5;">
                    <h4 class="mt-3 text-muted">No patients found</h4>
                    <p class="text-muted">Start by adding your first patient to the system.</p>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Add New Patient
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this patient? This action cannot be undone.</p>
                <p class="mb-0"><strong>Warning:</strong> All medical records, appointments, and other data associated with this patient will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete Patient</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .table th {
        font-weight: 600;
    }
    
    .dropdown-menu {
        min-width: 200px;
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
    }
</style>
@endsection

@section('scripts')
<script>
    // Confirmation for patient deletion
    document.addEventListener('DOMContentLoaded', function() {
        let deleteForm = null;
        
        // When delete button is clicked, store the form and show confirmation modal
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                deleteForm = this;
                const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                modal.show();
            });
        });
        
        // When deletion is confirmed in the modal
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deleteForm) {
                deleteForm.submit();
            }
        });
        
        // For appointment scheduling, prefill the patient name
        const newAppointmentModal = document.getElementById('newAppointmentModal');
        if (newAppointmentModal) {
            newAppointmentModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const patientId = button.getAttribute('data-patient-id');
                const patientName = button.getAttribute('data-patient-name');
                
                const patientSelect = this.querySelector('select[name="patient_id"]');
                if (patientSelect && patientId) {
                    patientSelect.value = patientId;
                }
            });
        }
    });
</script>
@endsection
