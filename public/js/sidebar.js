/**
 * Sidebar functionality for Ovum Doctor
 * 
 * This script handles sidebar toggle, mobile hamburger menu,
 * and responsive interactions for the dashboard sidebar.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarHamburger = document.getElementById('sidebarHamburger');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const topNav = document.querySelector('.top-nav');

    // Initialize sidebar state from localStorage if available
    if (sidebar && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
        if (sidebarToggle && sidebarToggle.querySelector('i')) {
            sidebarToggle.querySelector('i').classList.remove('fa-chevron-right');
            sidebarToggle.querySelector('i').classList.add('fa-chevron-left');
        }
    }
    
    // Sidebar toggle functionality
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            if (mainContent) {
                mainContent.classList.toggle('expanded');
            }
            
            // Update icon direction - When collapsed, show right arrow (->)
            // When expanded, show left arrow (<-)
            const icon = this.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.remove('fa-chevron-right');
                    icon.classList.add('fa-chevron-left');
                } else {
                    icon.classList.remove('fa-chevron-left');
                    icon.classList.add('fa-chevron-right');
                }
            }
            
            // Save sidebar state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            
            // Update calendar size after sidebar resize if calendar exists
            if (typeof calendar !== 'undefined' && calendar) {
                setTimeout(() => {
                    calendar.updateSize();
                }, 300); // Wait for transition to complete
            }
        });
    }
    
    // Mobile hamburger menu for sidebar
    if (sidebarHamburger && sidebar) {
        sidebarHamburger.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent document click from closing immediately
            
            // If top nav is expanded, close it first
            if (topNav && topNav.classList.contains('active')) {
                topNav.classList.remove('active');
            }
            
            sidebar.classList.toggle('mobile-visible');
            
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('visible');
            }
            
            // Toggle hamburger icon
            const icon = this.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('mobile-visible')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
    
    // Close sidebar when clicking overlay
    if (sidebarOverlay && sidebar && sidebarHamburger) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-visible');
            sidebarOverlay.classList.remove('visible');
            
            // Reset hamburger icon
            const icon = sidebarHamburger.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        // Handle mobile/desktop transitions
        if (window.innerWidth >= 768 && sidebar) {
            // Reset mobile sidebar visibility when returning to desktop
            sidebar.classList.remove('mobile-visible');
            if (sidebarHamburger) {
                const icon = sidebarHamburger.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('visible');
            }
        }
        
        // Update calendar size if it exists
        if (typeof calendar !== 'undefined' && calendar) {
            calendar.updateSize();
        }
    });
    
    // Add hover effect for sidebar when collapsed to show tooltips
    if (sidebar) {
        const navLinks = sidebar.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            // Get text content from the nav-text span
            const navText = link.querySelector('.nav-text');
            if (!navText) return;
            
            // Create tooltip element for each nav item
            const tooltip = document.createElement('div');
            tooltip.className = 'sidebar-tooltip';
            tooltip.textContent = navText.textContent;
            link.appendChild(tooltip);
            
            // Show tooltip on hover in collapsed state
            link.addEventListener('mouseenter', function() {
                if (sidebar.classList.contains('collapsed')) {
                    tooltip.style.display = 'block';
                    setTimeout(() => {
                        tooltip.style.opacity = '1';
                    }, 10);
                }
            });
            
            // Hide tooltip when mouse leaves
            link.addEventListener('mouseleave', function() {
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    tooltip.style.display = 'none';
                }, 300);
            });
        });
    }
    
    // Handle fullscreen calendar mode
    document.addEventListener('click', function(e) {
        if (e.target.closest('#toggleFullscreen')) {
            if (sidebar) {
                const fullscreenEl = document.querySelector('.fullscreen');
                if (fullscreenEl) {
                    // If entering fullscreen
                    if (window.innerWidth < 768) {
                        sidebar.style.zIndex = '0'; // Hide sidebar
                    }
                } else {
                    // If exiting fullscreen
                    sidebar.style.zIndex = '990'; // Restore sidebar z-index
                }
            }
        }
    });
    
    // Ensure proper z-index when top nav is expanded
    const topNavHamburger = document.querySelector('.hamburger');
    if (topNavHamburger && sidebar) {
        topNavHamburger.addEventListener('click', function() {
            // If sidebar is visible on mobile, hide it when expanding top nav
            if (window.innerWidth < 768 && sidebar.classList.contains('mobile-visible')) {
                sidebar.classList.remove('mobile-visible');
                sidebarOverlay.classList.remove('visible');
                
                // Reset sidebar hamburger icon
                const icon = sidebarHamburger.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
});

// Add tooltip styles to the document if they don't exist
document.addEventListener('DOMContentLoaded', function() {
    if (!document.getElementById('sidebar-tooltip-styles')) {
        const style = document.createElement('style');
        style.id = 'sidebar-tooltip-styles';
        style.textContent = `
            .sidebar-tooltip {
                position: absolute;
                left: 70px;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0,0,0,0.8);
                color: white;
                padding: 5px 10px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
                z-index: 1000;
            }
            
            .sidebar-tooltip:before {
                content: '';
                position: absolute;
                left: -5px;
                top: 50%;
                transform: translateY(-50%);
                border-right: 5px solid rgba(0,0,0,0.8);
                border-top: 5px solid transparent;
                border-bottom: 5px solid transparent;
            }
            
            .sidebar .nav-link {
                position: relative;
            }
            
            /* Space for top nav in fixed sidebar */
            body {
                min-height: 100vh;
                overflow-x: hidden;
            }
            
            /* Adjust fullscreen mode to work with fixed sidebar */
            .fullscreen {
                z-index: 1000 !important; /* Between sidebar and top nav */
            }
        `;
        document.head.appendChild(style);
    }
}); 