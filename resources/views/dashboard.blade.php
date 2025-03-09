@extends('layouts.app')

@section('title', 'Dashboard - Ovum Doctor')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Welcome Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="welcome-text">Welcome back, Dr. {{ Auth::user()->name }}!</h4>
        <div class="action-buttons">
            <button class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-envelope"></i> Messages
            </button>
            <button class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-bell"></i>
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                <i class="fas fa-plus me-1"></i>New Appointment
            </button>
        </div>
    </div>
    {{-- {{dd($todayAppointments, $appointmentIncrease, $completedAppointments, $completionRate, $pendingAppointments, $nextAppointmentIn, $cancelledAppointments, $todayPatients, $calendarEvents)}} --}}
    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-subtitle">Today's Appointments</h6>
                    <h2 class="card-title mb-2">{{ $todayAppointments }}</h2>
                    <p class="card-text"><i class="fas fa-arrow-up me-1"></i>{{ $appointmentIncrease }}%
                        increase</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-subtitle">Completed</h6>
                    <h2 class="card-title mb-2">{{ $completedAppointments }}</h2>
                    <p class="card-text">{{ $completionRate }}% of daily target</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-subtitle">Pending</h6>
                    <h2 class="card-title mb-2">{{ $pendingAppointments }}</h2>
                    <p class="card-text">Next in {{ $nextAppointmentIn }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-subtitle">Cancelled</h6>
                    <h2 class="card-title mb-2">{{ $cancelledAppointments }}</h2>
                    <p class="card-text">Today's cancellations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar and Patient List -->
    <div class="row g-3">
        <div class="col-md-8">
            <div class="calendar-container" id="expandableDiv">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Appointment Calendar</h5>
                    <div class="d-flex">
                        <button class="btn btn-sm me-2 fullscreen-btn" id="toggleFullscreen">
                            <i class="fas fa-expand"></i>
                        </button>
                        <div class="calendar-nav-links">
                            <a href="javascript:void(0)" class="calendar-nav-link active" id="calendarViewMonth">Monthly</a>
                            <span class="calendar-nav-separator">|</span>
                            <a href="javascript:void(0)" class="calendar-nav-link" id="calendarViewWeek">Weekly</a>
                            <span class="calendar-nav-separator">|</span>
                            <a href="javascript:void(0)" class="calendar-nav-link" id="calendarViewDay">Daily</a>
                        </div>
                    </div>
                </div>
                <div id="calendar" class="responsive-calendar"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="patient-list">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Today's Patients</h5>
                    <a href="{{ route('appointments.today') }}" class="btn btn-link btn-sm p-0">View All</a>
                </div>
                <div class="list-group patient-appointments-list">
                    @foreach ($todayPatients as $patient)
                        <a href="{{ route('patients.show', $patient->id) }}"
                            class="list-group-item list-group-item-action">
                            @foreach ($patient->appointments as $appointment)
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="mb-1 patient-name">{{ $patient->name }}</h6>
                                        <p class="mb-1 appointment-type">{{ $appointment->appointment_type }}</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="appointment-time mb-1">{{ $appointment->start_time }}</div>
                                        <span class="appointment-status-badge appointment-{{ strtolower($appointment->status) }}">
                                            {{ $appointment->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- New Appointment Modal -->
    @include('appointments.partials.create-modal')
    @include('appointments.partials.show-modal')
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Improved fullscreen toggle
            const expandableDiv = document.getElementById('expandableDiv');
            const toggleFullscreenBtn = document.getElementById('toggleFullscreen');
            
            function toggleFullscreen() {
                const icon = toggleFullscreenBtn.querySelector('i');
                
                if (expandableDiv.classList.contains('fullscreen')) {
                    expandableDiv.classList.remove('fullscreen');
                    expandableDiv.classList.add('calendar-container');
                    icon.classList.remove('fa-compress');
                    icon.classList.add('fa-expand');
                } else {
                    expandableDiv.classList.remove('calendar-container');
                    expandableDiv.classList.add('fullscreen');
                    icon.classList.remove('fa-expand');
                    icon.classList.add('fa-compress');
                }
                
                // Ensure calendar resizes properly
                setTimeout(() => {
                    calendar.updateSize();
                }, 300);
            }
            
            if (toggleFullscreenBtn) {
                toggleFullscreenBtn.addEventListener('click', toggleFullscreen);
            }

            // prepare to load appointment modal 
            function loadAppointmentDetails(appointmentId) {
                $.get(`/appointments/${appointmentId}`, function(response) {
                    $('#appointmentModalContent').html(response);
                    $('#appointmentModal').modal('show');
                });
            }

            // calendar function
            console.log("Creating Calendar");
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right:'none'
                },
                handleWindowResize: true,
                expandRows: true,
                stickyHeaderDates: true,
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: 1, // Limit visible events per day and show +more link
                scrollTime: '00:00',
                slotDuration: '00:30:00',
                firstDay: 1,

                eventMouseEnter: function(info) {
                    info.el.style.cursor = 'pointer';
                },
                eventContent: function(arg) {
                    return {
                        html: `
                        <div class="custom-event">
                            <span class="custom-event-dot"></span>
                            ${arg.event.title}
                        </div>
                    `
                    }
                },
                events: @json($calendarEvents),
                select: function(info) {
                    $('#createAppointmentModal').modal('show');
                    $('#createAppointmentForm').on('submit', function(e) {
                        e.preventDefault();

                        $.ajax({
                            url: $(this).attr('action'),
                            method: 'POST',
                            data: $(this).serialize(),
                            success: function(response) {
                                if (response.success) {
                                    // Add the new event to the calendar
                                    calendar.addEvent(response.event);

                                    // Update modal content
                                    $('#appointmentModalContent').html(response
                                        .modalContent);

                                    // Hide create modal and show details modal
                                    $('#createAppointmentModal').modal('hide');
                                    $('#appointmentModal').modal('show');

                                    // Optional: Clear the form
                                    $('#createAppointmentForm')[0].reset();
                                }
                            },
                            error: function(xhr) {
                                // Handle errors (show validation messages, etc.)
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON;
                                    // Display errors to user
                                    alert(errors.message ||
                                        'Error creating appointment');
                                }
                            }
                        });
                    });
                },
                eventClick: function(info) {
                    loadAppointmentDetails(info.event.id);
                },
                eventDrop: function(info) {
                    axios.patch(`/appointments/${info.event.id}/reschedule`, {
                        start: info.event.startStr
                    }).catch(function(error) {
                        info.revert();
                        alert('Failed to reschedule appointment');
                    });
                },
                // Properly style more link to be visible
                moreLinkContent: function(args) {
                    return `+${args.num} more`;
                }
            });

            calendar.render();

            // Set active class for the selected view
            const viewButtons = {
                month: document.getElementById('calendarViewMonth'),
                week: document.getElementById('calendarViewWeek'),
                day: document.getElementById('calendarViewDay')
            };
            
            function setActiveViewButton(activeButton) {
                // Remove active class from all buttons
                Object.values(viewButtons).forEach(btn => {
                    if (btn) {
                        btn.classList.remove('active');
                    }
                });
                
                // Add active class to clicked button
                if (activeButton) {
                    activeButton.classList.add('active');
                }
            }

            // View buttons functionality
            if (viewButtons.month) {
                viewButtons.month.addEventListener('click', function() {
                    calendar.changeView('dayGridMonth');
                    setActiveViewButton(this);
                });
            }
            
            if (viewButtons.week) {
                viewButtons.week.addEventListener('click', function() {
                    calendar.changeView('timeGridWeek');
                    setActiveViewButton(this);
                });
            }
            
            if (viewButtons.day) {
                viewButtons.day.addEventListener('click', function() {
                    calendar.changeView('timeGridDay');
                    setActiveViewButton(this);
                });
            }
            
            // Set month view as active by default
            setActiveViewButton(viewButtons.month);

            // Improved scroll handling
            document.getElementById('calendar').addEventListener('wheel', function(e) {
                if (Math.abs(e.deltaY) < 70) {
                    return;
                }

                if (e.deltaY > 0) {
                    calendar.next();
                } else {
                    calendar.prev();
                }
                e.preventDefault();
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Escape key exits fullscreen
                if (e.key === 'Escape' && expandableDiv.classList.contains('fullscreen')) {
                    toggleFullscreen();
                }
            });
        });
    </script>
@endsection
