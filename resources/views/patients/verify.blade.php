@extends('layouts.app')

@section('title', 'Verify Patient Access - Ovum Doctor')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-secondary-custom text-white">
                    <h5 class="mb-0">Patient Verification Required</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary-custom mb-3"></i>
                        <h4>Access to Patient Records</h4>
                        <p class="text-muted">
                            To access <strong>{{ $patient->name }}</strong>'s medical records, 
                            you need to verify with the patient.
                        </p>
                    </div>

                    <div id="step-request" class="verification-step active">
                        <p class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            An OTP will be sent to the patient's phone for verification.
                        </p>
                        
                        <form id="request-otp-form">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Patient's Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                    value="{{ $patient->phone }}" required>
                                <div class="form-text">Please confirm this is the patient's current number</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-custom-primary" id="request-otp-btn">
                                    Send Verification Code
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="step-verify" class="verification-step" style="display: none;">
                        <p class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Verification code sent to the patient's phone.
                        </p>
                        
                        <form id="verify-otp-form">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            <input type="hidden" name="phone" id="hidden-phone">
                            
                            <div class="mb-3">
                                <label for="code" class="form-label">Enter Verification Code</label>
                                <div class="otp-input-container d-flex justify-content-between">
                                    <input type="text" class="form-control form-control-lg text-center otp-input" 
                                        id="code" name="code" maxlength="6" required>
                                </div>
                                <div class="form-text">Ask the patient for the code they received</div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="verify-otp-btn">
                                    Verify Access
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resend-otp-btn">
                                    Resend Code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Request OTP form submission
    $('#request-otp-form').on('submit', function(e) {
        e.preventDefault();
        
        const phone = $('#phone').val();
        const patientId = $('input[name="patient_id"]').val();
        
        $('#request-otp-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('patient.access.request') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                patient_id: patientId,
                phone: phone
            },
            success: function(response) {
                if (response.success) {
                    // Hide request step and show verification step
                    $('#step-request').hide();
                    $('#step-verify').show();
                    $('#hidden-phone').val(phone);
                    
                    // Focus on OTP input
                    $('#code').focus();
                } else {
                    alert('Error: ' + response.message);
                    $('#request-otp-btn').html('Send Verification Code').prop('disabled', false);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to send verification code';
                alert('Error: ' + error);
                $('#request-otp-btn').html('Send Verification Code').prop('disabled', false);
            }
        });
    });
    
    // Verify OTP form submission
    $('#verify-otp-form').on('submit', function(e) {
        e.preventDefault();
        
        const code = $('#code').val();
        const phone = $('#hidden-phone').val();
        const patientId = $('input[name="patient_id"]').val();
        
        $('#verify-otp-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('patient.access.verify') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                patient_id: patientId,
                phone: phone,
                code: code
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to patient records
                    window.location.href = response.redirect;
                } else {
                    alert('Error: ' + response.message);
                    $('#verify-otp-btn').html('Verify Access').prop('disabled', false);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to verify code';
                alert('Error: ' + error);
                $('#verify-otp-btn').html('Verify Access').prop('disabled', false);
            }
        });
    });
    
    // Resend OTP button
    $('#resend-otp-btn').on('click', function() {
        $('#step-verify').hide();
        $('#step-request').show();
        $('#request-otp-btn').html('Send Verification Code').prop('disabled', false);
    });
});
</script>
@endsection 