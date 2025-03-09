<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | {{ config('app.name') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="logo-container">
                <img src="{{ asset('assets/images/GynLogo(2).png') }}" alt="Logo">
            </div>
            
            <div class="login-header">
                <h3>OVUM DOCTOR</h3>
                <p class="text-muted">Welcome back! Please login to continue</p>
            </div>
            
            <form id="loginForm" action="{{ route('login.attempt') }}" method="POST" data-ajax>
                @csrf
                
                <div class="form-group">
                    <input type="text" id="clinic" name="clinic" placeholder=" " value="{{ old('clinic') }}">
                    <label for="clinic">Gyn Clinic</label>
                    @error('clinic')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="text" id="doctor_name" name="doctor_name" placeholder=" " value="{{ old('doctor_name') }}">
                    <label for="doctor_name">Doctor's Name</label>
                    @error('doctor_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="password" id="auth-field-input" name="auth_value" placeholder=" ">
                    <label for="auth-field-input">Password</label>
                    @error('auth_value')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Hidden input for login method -->
                <input type="hidden" name="login_method" value="password">

                <button type="submit" class="btn btn-login" id="login-button">
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Toast Container for Notifications -->
    <div id="toast-container" class="position-fixed top-0 end-0 p-3"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html> 