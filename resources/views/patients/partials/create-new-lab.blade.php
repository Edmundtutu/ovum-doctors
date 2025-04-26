<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>New Lab Request | Ovum Lab</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Third-party CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Application CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <div id="app">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ asset('assets/images/GynLogo(2).png') }}" alt="Medical Center Logo">
                </div>
                <div class="headings">
                    <h2>OVUM LAB</h2>
                    <h3>{{ Auth::user()->lab->name ?? 'Laboratory Services' }}</h3>
                </div>
            </div>
            
            <div class="nav-links">
                <nav>
                    <a href="#" class="active">
                        <i class="fas fa-flask"></i> Dashboard
                    </a>
                    <a href="#">
                        <i class="fas fa-vial"></i> Tests
                    </a>
                    <a href="#">
                        <i class="fas fa-file-medical-alt"></i> Results
                    </a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" id="logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
                <button class="hamburger">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
        
        <!-- Content -->
        <div class="main-content" id="mainContent">
            <div class="container py-4">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h2 class="mb-2">Add New Lab Results</h2>
                        @if(isset($visit) && isset($visit->patient))
                        <p class="text-muted">
                            <i class="fas fa-user me-2"></i>Patient: <strong>{{ $visit->patient->name }}</strong> | 
                            <i class="fas fa-calendar-day me-2"></i>Visit: <strong>{{ $visit->visited_at ? $visit->visited_at->format('Y-m-d') : 'Not recorded' }}</strong> | 
                            <i class="fas fa-user-md me-2"></i>Doctor: <strong>Dr. {{ $visit->doctor->name }}</strong>
                        </p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary-custom text-white">
                        <h5 class="mb-0">Lab Request Form</h5>
                    </div>
                    <div class="card-body">
                        @if(!isset($visit) && !request()->has('visit_id'))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Please select a visit to associate with the lab results first.
                            </div>
                        @else
                            <form action="{{ isset($visit) ? route('visits.labs.store', $visit) : route('visits.labs.store', request('visit_id')) }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <!-- Basic Measurements -->
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2 mb-3">Basic Measurements</h6>
                                        
                                        <div class="mb-3">
                                            <label for="respiratory_rate" class="form-label">Respiratory Rate (breaths/min)</label>
                                            <input type="number" class="form-control @error('respiratory_rate') is-invalid @enderror" 
                                                id="respiratory_rate" name="respiratory_rate" value="{{ old('respiratory_rate') }}" min="8" max="30">
                                            @error('respiratory_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="hemoglobin" class="form-label">Hemoglobin (g/dL)</label>
                                            <input type="number" step="0.1" class="form-control @error('hemoglobin') is-invalid @enderror" 
                                                id="hemoglobin" name="hemoglobin" value="{{ old('hemoglobin') }}">
                                            @error('hemoglobin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="bp_systolic" class="form-label">BP Systolic (mmHg)</label>
                                            <input type="number" class="form-control @error('bp_systolic') is-invalid @enderror" 
                                                id="bp_systolic" name="bp_systolic" value="{{ old('bp_systolic') }}">
                                            @error('bp_systolic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="bp_diastolic" class="form-label">BP Diastolic (mmHg)</label>
                                            <input type="number" class="form-control @error('bp_diastolic') is-invalid @enderror" 
                                                id="bp_diastolic" name="bp_diastolic" value="{{ old('bp_diastolic') }}">
                                            @error('bp_diastolic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="waist_hip_ratio" class="form-label">Waist-Hip Ratio</label>
                                            <input type="number" step="0.01" class="form-control @error('waist_hip_ratio') is-invalid @enderror" 
                                                id="waist_hip_ratio" name="waist_hip_ratio" value="{{ old('waist_hip_ratio') }}">
                                            @error('waist_hip_ratio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Hormone Tests -->
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2 mb-3">Hormone Tests</h6>
                                        
                                        <div class="mb-3">
                                            <label for="fsh" class="form-label">FSH (mIU/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('fsh') is-invalid @enderror" 
                                                id="fsh" name="fsh" value="{{ old('fsh') }}">
                                            @error('fsh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="lh" class="form-label">LH (mIU/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('lh') is-invalid @enderror" 
                                                id="lh" name="lh" value="{{ old('lh') }}">
                                            @error('lh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="fsh_lh_ratio" class="form-label">FSH/LH Ratio</label>
                                            <input type="number" step="0.01" class="form-control @error('fsh_lh_ratio') is-invalid @enderror" 
                                                id="fsh_lh_ratio" name="fsh_lh_ratio" value="{{ old('fsh_lh_ratio') }}">
                                            <small class="form-text text-muted">Can be calculated automatically from FSH and LH values</small>
                                            @error('fsh_lh_ratio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="amh" class="form-label">AMH (ng/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('amh') is-invalid @enderror" 
                                                id="amh" name="amh" value="{{ old('amh') }}">
                                            @error('amh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="tsh" class="form-label">TSH (mIU/L)</label>
                                            <input type="number" step="0.001" class="form-control @error('tsh') is-invalid @enderror" 
                                                id="tsh" name="tsh" value="{{ old('tsh') }}">
                                            @error('tsh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <!-- Pregnancy Indicators -->
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2 mb-3">Pregnancy Indicators</h6>
                                        
                                        <div class="mb-3">
                                            <label for="hcg_initial" class="form-label">HCG Initial (mIU/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('hcg_initial') is-invalid @enderror" 
                                                id="hcg_initial" name="hcg_initial" value="{{ old('hcg_initial') }}">
                                            @error('hcg_initial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="hcg_followup" class="form-label">HCG Follow-up (mIU/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('hcg_followup') is-invalid @enderror" 
                                                id="hcg_followup" name="hcg_followup" value="{{ old('hcg_followup') }}">
                                            <small class="form-text text-muted">Optional follow-up value</small>
                                            @error('hcg_followup')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="progesterone" class="form-label">Progesterone (ng/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('progesterone') is-invalid @enderror" 
                                                id="progesterone" name="progesterone" value="{{ old('progesterone') }}">
                                            @error('progesterone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Additional Tests -->
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2 mb-3">Additional Tests</h6>
                                        
                                        <div class="mb-3">
                                            <label for="prolactin" class="form-label">Prolactin (ng/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('prolactin') is-invalid @enderror" 
                                                id="prolactin" name="prolactin" value="{{ old('prolactin') }}">
                                            @error('prolactin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="vitamin_d3" class="form-label">Vitamin D3 (ng/mL)</label>
                                            <input type="number" step="0.01" class="form-control @error('vitamin_d3') is-invalid @enderror" 
                                                id="vitamin_d3" name="vitamin_d3" value="{{ old('vitamin_d3') }}">
                                            @error('vitamin_d3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="rbs" class="form-label">Random Blood Sugar (mg/dL)</label>
                                            <input type="number" step="0.01" class="form-control @error('rbs') is-invalid @enderror" 
                                                id="rbs" name="rbs" value="{{ old('rbs') }}">
                                            @error('rbs')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <!-- Ultrasound Findings -->
                                    <div class="col-md-6">
                                        <h6 class="border-bottom pb-2 mb-3">Ultrasound Findings</h6>
                                        
                                        <div class="mb-3">
                                            <label for="total_follicles" class="form-label">Total Follicles</label>
                                            <input type="number" class="form-control @error('total_follicles') is-invalid @enderror" 
                                                id="total_follicles" name="total_follicles" value="{{ old('total_follicles') }}">
                                            @error('total_follicles')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="avg_fallopian_size" class="form-label">Avg. Fallopian Size (mm)</label>
                                            <input type="number" step="0.1" class="form-control @error('avg_fallopian_size') is-invalid @enderror" 
                                                id="avg_fallopian_size" name="avg_fallopian_size" value="{{ old('avg_fallopian_size') }}">
                                            @error('avg_fallopian_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="endometrium" class="form-label">Endometrium Thickness (mm)</label>
                                            <input type="number" step="0.1" class="form-control @error('endometrium') is-invalid @enderror" 
                                                id="endometrium" name="endometrium" value="{{ old('endometrium') }}">
                                            @error('endometrium')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-custom-primary">Save Lab Results</button>
                                    <a href="{{ isset($patient) ? route('patients.show', $patient) : '#' }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    </div>

    <!-- Third-party Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Application Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.querySelector('.hamburger');
            const nav = document.querySelector('nav');
            
            if (hamburger && nav) {
                hamburger.addEventListener('click', function() {
                    nav.classList.toggle('show');
                });
            }
            
            // Auto-calculate FSH/LH ratio when both values are entered
            const fshInput = document.getElementById('fsh');
            const lhInput = document.getElementById('lh');
            const ratioInput = document.getElementById('fsh_lh_ratio');
            
            function calculateRatio() {
                const fsh = parseFloat(fshInput.value);
                const lh = parseFloat(lhInput.value);
                
                if (fsh && lh && lh > 0) {
                    const ratio = (fsh / lh).toFixed(2);
                    ratioInput.value = ratio;
                }
            }
            
            if (fshInput && lhInput && ratioInput) {
                fshInput.addEventListener('input', calculateRatio);
                lhInput.addEventListener('input', calculateRatio);
            }
        });

        // Notifications
        @if(session('success'))
            showNotification('success', '{{ session('success') }}');
        @endif

        @if(session('error'))
            showNotification('error', '{{ session('error') }}');
        @endif

        @if($errors->any())
            showNotification('error', '{{ $errors->first() }}');
        @endif
    </script>
</body>
</html>