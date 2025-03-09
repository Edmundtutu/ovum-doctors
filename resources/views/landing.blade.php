@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar py-4">
            <h4 class="mb-4">Dashboard</h4>
            <nav class="nav flex-column">
                <a class="nav-link active" href="#"><i class="fas fa-home me-2"></i>Home</a>
                <a class="nav-link" href="#"><i class="fas fa-calendar-alt me-2"></i>Appointments</a>
                <a class="nav-link" href="#"><i class="fas fa-user-md me-2"></i>Doctors</a>
                <a class="nav-link" href="#"><i class="fas fa-users me-2"></i>Patients</a>
                <a class="nav-link" href="#"><i class="fas fa-chart-line me-2"></i>Analytics</a>
                <a class="nav-link" href="#"><i class="fas fa-cog me-2"></i>Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-4">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card bg-primary-custom text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Patients</h5>
                            <h2 class="mb-0">{{ $totalPatients ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card bg-secondary-custom text-white">
                        <div class="card-body">
                            <h5 class="card-title">Appointments Today</h5>
                            <h2 class="mb-0">{{ $todayAppointments ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card" style="background-color: var(--success-color); color: white;">
                        <div class="card-body">
                            <h5 class="card-title">Available Doctors</h5>
                            <h2 class="mb-0">{{ $availableDoctors ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card" style="background-color: var(--complementary-color); color: white;">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <h2 class="mb-0">${{ number_format($totalRevenue ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar and Patient List -->
            <div class="row">
                <div class="col-md-8">
                    <div class="calendar-container mb-4">
                        <div id="calendar" class="responsive-calendar"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="patient-list">
                        <h5 class="mb-4">Recent Patients</h5>
                        <div class="list-group">
                            @forelse($recentPatients ?? [] as $patient)
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $patient->name }}</h6>
                                        <small>{{ $patient->appointment_date }}</small>
                                    </div>
                                    <p class="mb-1">{{ $patient->condition }}</p>
                                </a>
                            @empty
                                <p class="text-muted">No recent patients</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [], // This will be populated from your backend
        eventClick: function(info) {
            // Handle event click
        },
        dateClick: function(info) {
            // Handle date click
        }
    });
    calendar.render();
});
</script>
@endpush 