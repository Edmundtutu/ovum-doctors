@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Appointments</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appointment)
            <tr>
                <td>{{ $appointment->patient->name }}</td>
                <td>{{ $appointment->doctor->name }}</td>
                <td>{{ $appointment->appointment_date->format('F j, Y') }}</td>
                <td>
                    <button class="btn btn-info" data-toggle="modal" data-target="#appointmentModal" 
                            data-appointment="{{ json_encode($appointment) }}">
                        View Details
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentModalLabel">Appointment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Date:</strong> <span id="modal-appointment-date"></span></p>
                <p><strong>Start Time:</strong> <span id="modal-start-time"></span></p>
                <p><strong>End Time:</strong> <span id="modal-end-time"></span></p>
                <p><strong>Type:</strong> <span id="modal-type"></span></p>
                <p><strong>Status:</strong> <span id="modal-status"></span></p>
                <p><strong>Reason:</strong> <span id="modal-reason"></span></p>
                <p><strong>Notes:</strong> <span id="modal-notes"></span></p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $('#appointmentModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var appointment = button.data('appointment'); // Extract info from data-* attributes

        // Update the modal's content
        $('#modal-appointment-date').text(new Date(appointment.appointment_date).toLocaleDateString());
        $('#modal-start-time').text(new Date(appointment.start_time).toLocaleTimeString());
        $('#modal-end-time').text(new Date(appointment.end_time).toLocaleTimeString());
        $('#modal-type').text(appointment.type);
        $('#modal-status').text(appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1));
        $('#modal-reason').text(appointment.reason);
        $('#modal-notes').text(appointment.notes);
    });
</script>
@endsection
