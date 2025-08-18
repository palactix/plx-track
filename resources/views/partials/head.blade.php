<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="apple-touch-icon" sizes="180x180" href="/fav-icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/fav-icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/fav-icons/favicon-16x16.png">
<link rel="manifest" href="/fav-icons/site.webmanifest">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

@yield('head')

{{-- Chart.js for analytics charts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

{{-- Toast Notification Styles --}}

@if(env('APP_ENV') === 'production')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FR7EN7LCR8"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-FR7EN7LCR8');
    </script>
        
@endif

<style>
.toast-container {
    position: fixed;
    bottom: 1.25rem;
    right: 1.25rem;
    z-index: 50;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-width: 24rem;
    pointer-events: none;
}

.toast {
    pointer-events: auto;
    transform: translateX(100%);
    transition: all 0.3s ease-in-out;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(8px);
    border: 1px solid;
}

.toast.show {
    transform: translateX(0);
}

.toast.success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95));
    border-color: rgba(16, 185, 129, 0.3);
    color: white;
}

.toast.error {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95));
    border-color: rgba(239, 68, 68, 0.3);
    color: white;
}

.toast.warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.95), rgba(217, 119, 6, 0.95));
    border-color: rgba(245, 158, 11, 0.3);
    color: white;
}

.toast.info {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95));
    border-color: rgba(59, 130, 246, 0.3);
    color: white;
}
</style>

{{-- Global Alpine.js Store for Notifications --}}
<script>
// Simple notification system that works reliably
window.NotificationSystem = {
    container: null,
    
    init() {
        // Create container if it doesn't exist
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        }
    },
    
    show(message, type = 'info', duration = 5000) {
        this.init();
        
        const toast = document.createElement('div');
        toast.className = `toast ${type} p-4 flex items-start gap-3 min-w-80`;
        
        const icons = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        
        toast.innerHTML = `
            <div class="flex-shrink-0">${icons[type]}</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-white/70 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;
        
        this.container.appendChild(toast);
        
        // Trigger animation
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Auto remove
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
        
        return toast;
    },
    
    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    },
    
    error(message, duration = 7000) {
        return this.show(message, 'error', duration);
    },
    
    warning(message, duration = 6000) {
        return this.show(message, 'warning', duration);
    },
    
    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
};

// Make it globally available
window.notify = window.NotificationSystem;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.notify.init();
    console.log('Notification system initialized');
    
    // Test notification to verify it works
    setTimeout(() => {
        console.log('Testing notification system...');
    }, 1000);
});

document.addEventListener('alpine:init', () => {
    // Also create Alpine store for compatibility
    Alpine.store('notifications', {
        items: [],
        
        add(message, type = 'info', duration = 5000) {
            return window.notify.show(message, type, duration);
        },
        
        success(message, duration = 5000) {
            return window.notify.success(message, duration);
        },
        
        error(message, duration = 7000) {
            return window.notify.error(message, duration);
        },
        
        warning(message, duration = 6000) {
            return window.notify.warning(message, duration);
        },
        
        info(message, duration = 5000) {
            return window.notify.info(message, duration);
        }
    });
});

// Copy to clipboard function
window.copyLinkToClipboard = function(url, button) {
    navigator.clipboard.writeText(url).then(() => {
        window.notify.success('Link copied to clipboard!');
        
        // Visual feedback on button
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        button.classList.add('text-green-500');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('text-green-500');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        window.notify.error('Failed to copy link. Please try again.');
    });
};

// Listen for Livewire events
document.addEventListener('livewire:initialized', () => {
    Livewire.on('notify', (data) => {
        const { message, type = 'info' } = data[0];
        window.notify[type](message);
    });
});
</script>

<style>
    .toast-container {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        z-index: 9999;
        pointer-events: none;
        max-width: 400px;
    }
    
    .toast {
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(10px);
        color: white;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-in-out;
        pointer-events: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    
    .toast.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .toast.success {
        background: rgba(34, 197, 94, 0.9);
        border-left: 4px solid #22c55e;
    }
    
    .toast.error {
        background: rgba(239, 68, 68, 0.9);
        border-left: 4px solid #ef4444;
    }
    
    .toast.warning {
        background: rgba(245, 158, 11, 0.9);
        border-left: 4px solid #f59e0b;
    }
    
    .toast.info {
        background: rgba(59, 130, 246, 0.9);
        border-left: 4px solid #3b82f6;
    }
</style>