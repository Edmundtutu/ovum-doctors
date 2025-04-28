<!-- Top Navigation -->
<div class="top-nav">
    <div class="logo-section">
        <div class="logo">
            <img src="{{ asset('assets/images/GynLogo(2).png') }}" alt="Medical Center Logo">
        </div>
        <div class="headings">
            <h2>OVUM LAB</h2>
        </div>
    </div>
    
    <div class="nav-links">
        <nav>
            <a href="{{ route('lab.dashboard') }}" class="{{ request()->routeIs('lab.dashboard') ? 'active' : '' }}">
                <i class="fas fa-flask"></i> Dashboard
            </a>
            <a href="{{ route('lab.tests') }}" class="{{ request()->routeIs('lab.tests.*') ? 'active' : '' }}">
                <i class="fas fa-vial"></i> Tests
            </a>
            <a href="{{ route('lab.results') }}" class="{{ request()->routeIs('lab.results.*') ? 'active' : '' }}">
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