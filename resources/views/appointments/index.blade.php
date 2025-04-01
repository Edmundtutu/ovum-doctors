@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Appointments</h1>
        <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
            <i class="fas fa-plus me-2"></i>Add Appointment
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Sorted by the Closest appointment to the current date and time --}}
                        @foreach($appointments->sortBy(function($appointment) {
                            $dateDiff = $appointment->appointment_date->startOfDay()->diffInDays(now()->startOfDay(), false);
                            $timeDiff = $appointment->start_time->diffInMinutes(now()->format('H:i:s'), false);
                            return abs($dateDiff) * 1440 + abs($timeDiff); // Convert days to minutes and add time difference
                        }) as $appointment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-circle text-bg-secondary-custom me-2">
                                            {{ substr($appointment->patient->name, 0, 1) }}
                                        </span>
                                    </div>
                                    {{ $appointment->patient->name }}
                                </div>
                            </td>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>
                                <div class="text-nowrap">
                                    {{ $appointment->appointment_date->format('M j, Y') }}
                                    <div class="text-muted small">
                                        {{ $appointment->start_time->format('g:i A') }} - 
                                        {{ $appointment->end_time->format('g:i A') }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $appointment->type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                    @switch($appointment->status)
                                        @case('confirmed') bg-success @break
                                        @case('pending') bg-warning @break
                                        @case('cancelled') bg-danger @break
                                        @case('completed') bg-primary @break
                                        @default bg-secondary
                                    @endswitch">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-appointment" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#appointmentModal"
                                        data-appointment-id="{{ $appointment->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary edit-appointment"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editAppointmentModal"
                                        data-appointment-id="{{ $appointment->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- New Appointment Modal -->
@include('appointments.partials.create-modal')
@include('appointments.partials.show-modal')
@include('appointments.partials.edit-modal')
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
document.addEventListener('DOMContentLoaded', function() {
    // Handle View Appointment button clicks
    document.querySelectorAll('.view-appointment').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            
            // Show loading state
            document.getElementById('appointmentModalContent').innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Fetch appointment details
            fetch(`/appointments/${appointmentId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('appointmentModalContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('appointmentModalContent').innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load appointment details. Please try again.
                    </div>
                `;
            });
        });
    });

    // New: Handle Edit Appointment button clicks
    document.querySelectorAll('.edit-appointment').forEach(button => {
        button.addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            
            // Show loading state
            document.getElementById('editAppointmentModalContent').innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary-custom" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // Fetch appointment edit form
            fetch(`/appointments/${appointmentId}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('editAppointmentModalContent').innerHTML = html;
                
                // Initialize form handlers for the edit form
                const editForm = document.getElementById('editAppointmentForm');
                if (editForm) {
                    initializeEditFormHandlers(editForm);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('editAppointmentModalContent').innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load edit form. Please try again.
                    </div>
                `;
            });
        });
    });

    // Function to initialize edit form handlers
    function initializeEditFormHandlers(form) {
        // Update end time when start time changes
        const startTimeInput = document.getElementById('edit_start_time');
        const endTimeInput = document.getElementById('edit_end_time');
        
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                const startTime = this.value;
                if (startTime) {
                    const [hours, minutes] = startTime.split(':');
                    const date = new Date();
                    date.setHours(hours);
                    date.setMinutes(minutes);
                    date.setMinutes(date.getMinutes() + 45);
                    
                    const endHours = String(date.getHours()).padStart(2, '0');
                    const endMinutes = String(date.getMinutes()).padStart(2, '0');
                    endTimeInput.value = `${endHours}:${endMinutes}`;
                }
            });
        }

        // Handle edit form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorDiv = document.getElementById('editAppointmentError');
            errorDiv.classList.add('d-none');
            formData.append('_method', 'PUT');

            for (const [key, value] of formData.entries()) {
                console.log('Sending:', key, value);
            }
            
            fetch(this.action, {
                method: 'POST', 
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw data;
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update calendar if it exists (when on dashboard)
                    if (window.calendar) {
                        // Find existing event with same ID
                        const existingEvent = window.calendar.getEventById(data.appointment.id);
                        if (existingEvent) {
                            // Update existing event
                            existingEvent.setProp('title', data.appointment.patient.name);
                            existingEvent.setStart(data.appointment.appointment_date + 'T' + data.appointment.start_time);
                            existingEvent.setEnd(data.appointment.appointment_date + 'T' + data.appointment.end_time);
                        }
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editAppointmentModal'));
                    if (modal) modal.hide();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Appointment updated successfully',
                            icon: 'success'
                        }).then(() => {
                            // Reload page to show updated data
                            window.location.reload();
                        });
                    } else {
                        alert(data.message || 'Appointment updated successfully');
                        window.location.reload();
                    }
                } else {
                    errorDiv.textContent = (data && data.message) || 'An error occurred while updating the appointment.';
                    errorDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.message) {
                    errorDiv.textContent = error.message;
                } else if (error.errors) {
                    // Format validation errors
                    const errorMessages = [];
                    for (const field in error.errors) {
                        errorMessages.push(error.errors[field][0]);
                    }
                    errorDiv.textContent = errorMessages.join(' ');
                } else {
                    errorDiv.textContent = 'An error occurred while updating the appointment.';
                }
                errorDiv.classList.remove('d-none');
            });
        });
    }
    
    // Initialize create appointment form handlers
    const createForm = document.getElementById('createAppointmentForm');
    if (createForm) {
        // Set default end time 45 minutes after start time
        document.getElementById('start_time').addEventListener('change', function() {
            const startTime = this.value;
            if (startTime) {
                const [hours, minutes] = startTime.split(':');
                const date = new Date();
                date.setHours(hours);
                date.setMinutes(minutes);
                date.setMinutes(date.getMinutes() + 45);
                
                const endHours = String(date.getHours()).padStart(2, '0');
                const endMinutes = String(date.getMinutes()).padStart(2, '0');
                document.getElementById('end_time').value = `${endHours}:${endMinutes}`;
            }
        });

        // Handle form submission
        createForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const errorDiv = document.getElementById('appointmentError');
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                return Promise.reject('Non-JSON response');
            })
            .then(data => {
                // Handle success
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => {
                // Unified error handling
                if (error instanceof Response) {
                    error.json().then(errData => {
                        showErrors(errData);
                    });
                } else {
                    console.error('Error:', error);
                    errorDiv.textContent = typeof error === 'string' ? error : 'Request failed';
                    errorDiv.classList.remove('d-none');
                }
            });
        });
    }
});
</script>
@endsection