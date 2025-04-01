<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAppointmentModalLabel">Schedule New Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAppointmentForm" method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="patient_id" class="form-label">Patient</label>
                            {{-- {{dd(auth()->guard('doctor')->user()->patients)}} --}}
                            <select class="form-select" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach(auth()->guard('doctor')->user()->patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="appointment_date" class="form-label">Appointment Date</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                   min="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Appointment Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select Type</option>
                            {{--  TODO :We'll have to change this to fetch from the Db When we ge types of appointments --}}
                            <option value="Consultation">Consultation</option>
                            <option value="Follow-up">Follow-up</option>
                            <option value="Check-up">Check-up</option>
                            <option value="Treatment">Treatment</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Visit</label>
                        <textarea class="form-control" id="reason" name="reason" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>

                    <div class="alert alert-danger d-none" id="appointmentError"></div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="scheduleAppointmentBtn">Schedule Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createAppointmentForm');
    const errorDiv = document.getElementById('appointmentError');
    
    // Set default end time 45 minutes after start time as time for an appoinmnt shouldnt take so long
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
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const errorDiv = document.getElementById('appointmentError');
        errorDiv.classList.add('d-none');
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                // Add event to calendar with debugging
                if (window.calendar) {
                    console.log('Adding event to calendar:', data.event);
                    const newEvent = window.calendar.addEvent(data.event);
                    console.log('New event added:', newEvent);
                }

                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success'
                }).then(() => {
                    // Hide create modal
                    $('#createAppointmentModal').modal('hide');
                    
                    // Show appointment details modal if needed
                    if (data.modalContent) {
                        $('#appointmentModalContent').html(data.modalContent);
                        $('#appointmentModal').modal('show');
                    }
                    
                    // Reset form
                    form.reset();
                    errorDiv.classList.add('d-none');
                    
                    // Optionally reload the page or update specific sections
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // Update any necessary page elements
                        updateDashboardStats(); // You'll need to implement this
                    }
                });
            } else {
                // Show error message
                errorDiv.textContent = data.message;
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
                errorDiv.textContent = 'An error occurred while creating the appointment.';
            }
            errorDiv.classList.remove('d-none');
        });
    });

    // Add function to update dashboard stats
    function updateDashboardStats() {
        fetch('/dashboard/stats', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Update stats cards with new data
            document.querySelector('.stats-card:nth-child(1) h2').textContent = data.todayAppointments;
            document.querySelector('.stats-card:nth-child(2) h2').textContent = data.completedAppointments;
            document.querySelector('.stats-card:nth-child(3) h2').textContent = data.pendingAppointments;
            document.querySelector('.stats-card:nth-child(4) h2').textContent = data.cancelledAppointments;
        });
    }
});
</script>
@endpush 