<form id="editAppointmentForm" method="POST" action="{{ route('appointments.update', $appointment) }}">
    @csrf
    @method('PUT')
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="edit_patient_id" class="form-label">Patient</label>
            <select class="form-select" id="edit_patient_id" name="patient_id" required>
                <option value="">Select Patient</option>
                @foreach(auth()->guard('doctor')->user()->patients as $patient)
                    <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label for="edit_appointment_date" class="form-label">Appointment Date</label>
            <input type="date" class="form-control" id="edit_appointment_date" name="appointment_date"
                   min="{{ date('Y-m-d') }}" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="edit_start_time" class="form-label">Start Time</label>
            <input type="time" class="form-control" id="edit_start_time" name="start_time" 
                   value="{{ $appointment->start_time->format('H:i') }}" required>
        </div>
        <div class="col-md-6">
            <label for="edit_end_time" class="form-label">End Time</label>
            <input type="time" class="form-control" id="edit_end_time" name="end_time" 
                   value="{{ $appointment->end_time->format('H:i') }}" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="edit_type" class="form-label">Appointment Type</label>
        <select class="form-select" id="edit_type" name="type" required>
            <option value="">Select Type</option>
            <option value="Consultation" {{ $appointment->type == 'Consultation' ? 'selected' : '' }}>Consultation</option>
            <option value="Follow-up" {{ $appointment->type == 'Follow-up' ? 'selected' : '' }}>Follow-up</option>
            <option value="Check-up" {{ $appointment->type == 'Check-up' ? 'selected' : '' }}>Check-up</option>
            <option value="Treatment" {{ $appointment->type == 'Treatment' ? 'selected' : '' }}>Treatment</option>
            <option value="Emergency" {{ $appointment->type == 'Emergency' ? 'selected' : '' }}>Emergency</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="edit_status" class="form-label">Status</label>
        <select class="form-select" id="edit_status" name="status" required>
            <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="edit_reason" class="form-label">Reason for Visit</label>
        <textarea class="form-control" id="edit_reason" name="reason" rows="2" required>{{ $appointment->reason }}</textarea>
    </div>

    <div class="mb-3">
        <label for="edit_notes" class="form-label">Additional Notes</label>
        <textarea class="form-control" id="edit_notes" name="notes" rows="2">{{ $appointment->notes }}</textarea>
    </div>

    <div class="alert alert-danger d-none" id="editAppointmentError"></div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Appointment</button>
    </div>
</form> 