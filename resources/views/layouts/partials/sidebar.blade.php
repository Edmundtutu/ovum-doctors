<!-- Mobile hamburger menu -->
<button class="hamburger-menu" id="sidebarHamburger" title="Toggle Sidebar">
    <i class="fas fa-bars"></i>
</button>

<!-- Overlay for mobile sidebar -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="nav-header">
        <h5 class="nav-title">Ovum Doctor</h5>
        <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i><span class="nav-text">Dashboard</span>
        </a>
        <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}"
            href="{{ route('appointments.index') }}">
            <i class="fas fa-calendar"></i><span class="nav-text">Appointments</span>
        </a>
        <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}"
            href="{{ route('patients.index') }}">
            <i class="fas fa-users"></i><span class="nav-text">Patients</span>
        </a>
        <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}"
            href="{{ route('analytics') }}">
            <i class="fas fa-chart-line"></i><span class="nav-text">Analytics</span>
        </a>
        <a class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}"
            href="{{ route('settings') }}">
            <i class="fas fa-cog"></i><span class="nav-text">Settings</span>
        </a>
    </nav>
</div> 