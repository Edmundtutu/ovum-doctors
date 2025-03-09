<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Ovum Doctor')</title>

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
    @auth
        <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    @endauth
    @yield('styles')
</head>
<body class="bg-light">
    <div id="app">
        @auth
            @include('layouts.partials.nav')
            @include('layouts.partials.sidebar')
            
            <div class="main-content" id="mainContent">
                <!-- Main Content -->
                <main>
                    @yield('content')
                </main>
            </div>
        @else
            <!-- Main Content for unauthenticated users -->
            <main>
                @yield('content')
            </main>
        @endauth

        <!-- Toast Container -->
        <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    </div>

    <!-- Third-party Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <!-- Application Scripts -->
    <script src="{{ asset('js/main.js') }}"></script>
    @auth
        <script src="{{ asset('js/sidebar.js') }}"></script>
    @endauth

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
    @yield('scripts')
</body>
</html> 