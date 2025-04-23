@extends('layouts.app')

@section('title', 'Patient Details - Ovum Doctor')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
    <style>
        /* Timeline Custom Styles */
        .timeline {
            position: relative;
            max-height: 500px !important;
            overflow-y: auto;
            padding-right: 10px;
        }

        .timeline-item {
            position: relative;
            padding-left: 45px;
            margin-bottom: 20px;
        }

        .timeline-date {
            position: absolute;
            left: 0;
            width: 40px;
            text-align: center;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .timeline-content {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 12px;
            position: relative;
        }

        .timeline-content:before {
            content: "";
            position: absolute;
            left: -10px;
            top: 15px;
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-right: 10px solid #f8f9fa;
        }

        .symptom-tag {
            display: inline-block;
            padding: 2px 8px;
            margin-right: 5px;
            margin-bottom: 5px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .section-expandable {
            transition: all 0.3s ease;
        }

        .expand-toggle {
            cursor: pointer;
            color: #0d6efd;
            font-size: 1.2rem;
        }

        /* Symptom tag colors */
        .symptom-pain {
            background-color: #ffebee;
            color: #c62828;
        }

        .symptom-mood {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .symptom-flow {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .symptom-other {
            background-color: #fff8e1;
            color: #f57f17;
        }

        /* Calendar styles */
        .fc-event {
            cursor: pointer;
        }

        .fc-event-title {
            font-weight: 500;
        }

        .equal-section {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .equal-section > div {
            flex-grow: 1;
        }
    </style>
@endsection

@section('content')
    <!-- Patient Header -->
    <div class="patient-header p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2">{{ $patient->name }}</h2>
                <div class="d-flex gap-4 text-muted">
                    <span><i class="fas fa-calendar-days me-2"></i>{{ $patient->age }} years old (DOB:
                        {{ $patient->date_of_birth->format('Y-m-d') }})</span>
                    @if ($patient->medical_condition)
                        <span class="text-danger"><i
                                class="fas fa-circle-exclamation me-2"></i>{{ $patient->medical_condition }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#newVisitModal">Add New
                    Visit</button>
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
                            <small class="text-muted">Last checked:
                                {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
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
                            <small class="text-muted">Last checked:
                                {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
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
                            <small class="text-muted">Last checked:
                                {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
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
                            <small class="text-muted">Last checked:
                                {{ $patient->latest_vitals->checked_at ?? 'Never' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section, Calendar, and Timeline section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0">Patient History and Trends</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" id="expandToggle">
                            <i class="fas fa-expand expand-toggle"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body section-expandable" id="expandableSection">
                    <div class="row align-items-stretch">
                        <!-- Graph Column -->
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <h6 class="card-subtitle mb-3">Cycle Variations</h6>
                            <div class="equal-section">
                                <div class="chart-container">
                                    <div id="chart-container"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar Column -->
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <h6 class="card-subtitle mb-3">Calendar</h6>
                            <div class="equal-section">
                                <div class="calendar-container">
                                    <div id="patient-calendar" class="responsive-calendar"
                                        style="width:100%;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Column -->
                        <div class="col-lg-4">
                            <h6 class="card-subtitle mb-3">Symptom Timeline</h6>
                            <div class="equal-section">
                                <div class="timeline" id="symptomTimeline">
                                    <!-- Timeline items will be dynamically inserted here -->
                                </div>
                            </div>
                        </div>
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
                                        <small
                                            class="text-muted">{{ $visit->visited_at ? $visit->visited_at->format('Y-m-d') : 'Not Recorded' }}</small>
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
                                        <span
                                            class="status-badge bg-{{ $medication->status_color }} bg-opacity-10 text-{{ $medication->status_color }}">
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
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="vitals-chart"></canvas>
                    </div>
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
                                {{ $visit->type }} With Dr. {{ $visit->doctor->name }} -
                                {{ $visit->visted_at ? $visit->visited_at->format('Y-m-d') : 'Not Recoreded' }}
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
@php
    if (isset($cycleHistory) && $cycleHistory->count() > 0) {
        $months = $cycleHistory->pluck('month');
        $cycle_length = $cycleHistory->pluck('cycle_length');
        $period_length = $cycleHistory->pluck('period_length');
    } else {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
        $cycle_length = [28, 30, 27, 29, 28];
        $period_length = [5, 6, 5, 4, 5];
    }
@endphp


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Vitals Chart with error handling
            const vitalsChartCtx = document.getElementById('vitals-chart');
            if (vitalsChartCtx) {
                try {
                    const vitalsData = @json($vitalsHistory);
                    if (vitalsData && vitalsData.length > 0) {
                        new Chart(vitalsChartCtx, {
                            type: 'line',
                            data: {
                                labels: vitalsData.map(v => v.date),
                                datasets: [{
                                    label: 'Heart Rate',
                                    data: vitalsData.map(v => v.heart_rate),
                                    borderColor: '#0d6efd',
                                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                                    tension: 0.4
                                }, {
                                    label: 'Weight',
                                    data: vitalsData.map(v => v.weight),
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
                    } else {
                        vitalsChartCtx.parentElement.innerHTML =
                            '<div class="text-center text-muted py-3">No vitals data available</div>';
                    }
                } catch (error) {
                    console.error('Error initializing vitals chart:', error);
                    vitalsChartCtx.parentElement.innerHTML =
                        '<div class="text-center text-danger py-3">Error loading vitals data</div>';
                }
            }

            // Initialize Cycle Chart with error handling
            const cycleChartContainer = document.getElementById('chart-container');
            if (cycleChartContainer) {
                try {
                    const cycleData = @json($cycleHistory);
                    if (cycleData && cycleData.length > 0) {
                        Highcharts.chart('chart-container', {
                            accessibility: {
                                enabled: true,
                                description: 'Chart showing cycle length and period length over time'
                            },
                            title: {
                                text: null
                            },
                            xAxis: {
                                categories: cycleData.map(c => c.month),
                                title: {
                                    text: 'Month'
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'Days'
                                }
                            },
                            legend: {
                                enabled: true,
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            credits: {
                                enabled: false
                            },
                            series: [{
                                name: 'Cycle Length',
                                data: cycleData.map(c => c.cycle_length),
                                color: '#007bff'
                            }, {
                                name: 'Period Length',
                                data: cycleData.map(c => c.period_length),
                                color: '#dc3545'
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
                    } else {
                        cycleChartContainer.innerHTML =
                            '<div class="text-center text-muted py-3">No cycle history data available</div>';
                    }
                } catch (error) {
                    console.error('Error initializing cycle chart:', error);
                    cycleChartContainer.innerHTML =
                        '<div class="text-center text-danger py-3">Error loading cycle history data</div>';
                }
            }

            // Function to load and update symptom timeline
            function loadSymptomTimeline(patientId) {
                const timelineContainer = document.getElementById('symptomTimeline');
                if (!timelineContainer) return;

                try {
                    const cycleData = @json($cycleHistory);
                    if (cycleData && cycleData.length > 0) {
                        const timelineHTML = cycleData.map(entry => {
                            // Handle symptoms as a simple array of strings
                            const symptoms = Array.isArray(entry.symptoms) ? entry.symptoms : [];
                            const symptomsHTML = symptoms.map(symptom => {
                                // Determine symptom type based on the symptom text
                                const type = determineSymptomType(symptom);
                                const className = getSymptomClass(type);
                                return `<span class="symptom-tag ${className}">${symptom}</span>`;
                            }).join('');

                            return `
                                <div class="timeline-item">
                                    <div class="timeline-date">
                                        <small>${entry.month}</small>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="mb-2">${symptomsHTML || '<span class="text-muted">No symptoms recorded</span>'}</div>
                                        <small class="text-muted">Recorded in ${entry.month}</small>
                                    </div>
                                </div>
                            `;
                        }).join('');

                        timelineContainer.innerHTML = timelineHTML;
                    } else {
                        timelineContainer.innerHTML =
                            '<div class="text-center text-muted py-3">No symptom data available</div>';
                    }
                } catch (error) {
                    console.error('Error loading symptom timeline:', error);
                    timelineContainer.innerHTML =
                        '<div class="text-center text-danger py-3">Error loading symptom data</div>';
                }
            }

            // Helper function to determine symptom type based on the symptom text
            function determineSymptomType(symptom) {
                const symptomLower = symptom.toLowerCase();
                // We shall add symptom type determination logic here
                // This is a simple example - you might want to expand this based on your needs
                if (symptomLower.includes('pain') || symptomLower.includes('cramp')) return 'pain';
                if (symptomLower.includes('mood') || symptomLower.includes('irritab')) return 'mood';
                if (symptomLower.includes('flow') || symptomLower.includes('bleed')) return 'flow';
                return 'other';
            }

            // Helper function to get symptom class
            function getSymptomClass(type) {
                const classMap = {
                    'pain': 'symptom-pain',
                    'mood': 'symptom-mood',
                    'flow': 'symptom-flow',
                    'other': 'symptom-other'
                };
                return classMap[type] || 'symptom-other';
            }

            // Load symptom timeline on page load
            loadSymptomTimeline({{ $patient->id }});


            /**
             * Implement and render the appointments on the calendar
             */
            // Get patient ID from the URL
            const patientId = window.location.pathname.split('/').filter(segment => segment !== '')[1];
            // const patientId = {{ $patient->id }};

            // Get elements
            const calendarEl = document.getElementById('patient-calendar');

            // Initialize the calendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: '100%',
                dayMaxEvents: 3,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                },
                events: function(info, successCallback, failureCallback) {
                    // Fetch events for the current view's date range
                    fetch(`/patients/${patientId}/appointments?patient_id=${patientId}`)
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Map events to include custom properties for styling
                                const events = result.data.map(event => ({
                                    title: event.title,
                                    start: event.start,
                                    end: event.end,
                                    extendedProps: {
                                        type: event.type,
                                        status: event.status,
                                        reason: event.reason
                                    },
                                    className: `fc-event-${event.extendedProps.status.toLowerCase()}` 
                                }));
                                successCallback(events);
                            } else {
                                failureCallback(new Error('Failed to load events'));
                            }
                        })
                        .catch(error => {
                            console.error('Error loading calendar events:', error);
                            failureCallback(error);
                        });
                },
                eventClick: function(info) {
                    // Show appointment details in a modal or tooltip
                    const event = info.event;
                    const props = event.extendedProps;
                    alert(`
                        Appointment Details:
                        Time: ${event.start.toLocaleTimeString()}
                        Type: ${props.type}
                        Status: ${props.status}
                        Reason: ${props.reason}
                    `);
                },
                eventDidMount: function(info) {
                    // Add tooltips to events
                    info.el.title = `
                        Time: ${info.event.start.toLocaleTimeString()}
                        Type: ${info.event.extendedProps.type}
                        Status: ${info.event.extendedProps.status}
                    `;

                    // Add custom content inside the event
                    const eventContent = `
                        <div class="custom-event">
                            <span class="custom-event-dot"></span>
                            ${info.event.title}
                        </div>
                    `;
                    info.el.innerHTML = eventContent;
                }
            });

            // Render the calendar
            calendar.render();

           // Make timeline responsive
            function adjustTimelineHeight() {
                const timeline = document.getElementById('symptomTimeline');
                if (timeline) {
                    timeline.style.maxHeight = window.innerWidth < 992 ? '200px' : '300px';
                }
            }

            // Call on load and resize
            adjustTimelineHeight();
            window.addEventListener('resize', function() {
                adjustTimelineHeight();
                calendar.updateSize(); // Update calendar size on window resize
            });
        });
    </script>
@endsection
