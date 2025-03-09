
<div class="row">
    <!-- Patient & Doctor Info Card -->
    <div class="col-md-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-circle bg-primary-custom text-white me-3">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Patient: {{ $appointment->patient->name }}</h6>
                        <p class="text-muted mb-0">Doctor: Dr. {{ $appointment->doctor->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date & Time Info -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted-custom">
                    <i class="fas fa-clock me-2"></i>Schedule
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Date:</strong> 
                        <span class="badge bg-light text-dark">
                            {{ $appointment->appointment_date->format('F j, Y') }}
                        </span>
                    </li>
                    <li class="mb-2">
                        <strong>Time:</strong> 
                        <span class="badge bg-light text-dark">
                            {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Status & Type Info -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted-custom">
                    <i class="fas fa-info-circle me-2"></i>Details
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <strong>Type:</strong> 
                        <span class="badge bg-info">{{ $appointment->type }}</span>
                    </li>
                    <li class="mb-2">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning') }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Reason & Notes -->
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted-custom">
                    <i class="fas fa-notes-medical me-2"></i>Additional Information
                </h6>
                <div class="mb-3">
                    <strong>Reason:</strong>
                    <p class="mb-3">{{ $appointment->reason }}</p>
                </div>
                <div>
                    <strong>Notes:</strong>
                    <p class="mb-0">{{ $appointment->notes }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <a href="{{ route('appointments.edit', $appointment) }}" class="btn text-muted-custom">
        <i class="fas fa-edit me-2"></i>Edit
    </a>
    <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn text-error-custom">
            <i class="fas fa-trash-alt me-2"></i>Delete
        </button>
    </form>
    <button type="button" class="btn text-secondary" data-bs-dismiss="modal">Close</button>
</div>