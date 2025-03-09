// Mobile menu toggle
const hamburger = document.querySelector('.hamburger');
const topNav = document.querySelector('.top-nav');

hamburger?.addEventListener('click', () => {
    topNav.classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!topNav?.contains(e.target) && topNav?.classList.contains('active')) {
        topNav.classList.remove('active');
    }
});

// Close mobile menu when window is resized above mobile breakpoint
window.addEventListener('resize', () => {
    if (window.innerWidth > 767 && topNav?.classList.contains('active')) {
        topNav.classList.remove('active');
    }
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Handle form submissions with AJAX
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.hasAttribute('data-ajax')) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitButton = form.querySelector('[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('success', data.message || 'Operation completed successfully');
                
                // Handle redirect if specified
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
                
                // Handle form reset if specified
                if (data.reset) {
                    form.reset();
                }
                
                // Handle callback if specified
                if (typeof window[data.callback] === 'function') {
                    window[data.callback](data);
                }
            } else {
                throw new Error(data.message || 'Operation failed');
            }
        })
        .catch(error => {
            showNotification('error', error.message || 'An error occurred');
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    }
});

// Show notification
function showNotification(type, message) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    const container = document.getElementById('toast-container') || document.body;
    container.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Handle dynamic content loading
document.addEventListener('click', function(e) {
    const target = e.target.closest('[data-load-content]');
    if (target) {
        e.preventDefault();
        
        const url = target.getAttribute('data-load-content');
        const container = document.querySelector(target.getAttribute('data-container') || '#view-container');
        
        if (!container) return;
        
        // Show loading state
        container.innerHTML = '<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            
            // Reinitialize any plugins or scripts
            initializePlugins();
        })
        .catch(error => {
            container.innerHTML = `
                <div class="alert alert-danger m-3">
                    Failed to load content. Please try again.
                </div>
            `;
        });
    }
});

// Initialize plugins and scripts for dynamically loaded content
function initializePlugins() {
    // Reinitialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Reinitialize any other plugins here
}

// Handle confirmation dialogs
document.addEventListener('click', function(e) {
    const target = e.target.closest('[data-confirm]');
    if (target) {
        e.preventDefault();
        
        const message = target.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
        
        if (confirm(message)) {
            if (target.tagName === 'A') {
                window.location.href = target.href;
            } else if (target.tagName === 'BUTTON' || target.tagName === 'INPUT') {
                target.form?.submit();
            }
        }
    }
}); 