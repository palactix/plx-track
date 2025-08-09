{{-- Toast Notification Container --}}
<div class="toast-container" x-data x-show="$store.notifications.items.length > 0" style="display: none;">
    <template x-for="notification in $store.notifications.items" :key="notification.id">
        <div 
            class="toast p-4 flex items-start gap-3 min-w-80"
            :class="[notification.type, notification.show ? 'show' : '']"
            x-show="notification"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform translate-x-full opacity-0"
            x-transition:enter-end="transform translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform translate-x-0 opacity-100"
            x-transition:leave-end="transform translate-x-full opacity-0"
        >
            {{-- Icon --}}
            <div class="flex-shrink-0">
                <div x-show="notification.type === 'success'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div x-show="notification.type === 'error'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div x-show="notification.type === 'warning'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div x-show="notification.type === 'info'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Message --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium" x-text="notification.message"></p>
            </div>
            
            {{-- Close Button --}}
            <button 
                @click="$store.notifications.remove(notification.id)"
                class="flex-shrink-0 text-white/70 hover:text-white transition-colors"
            >
                <flux:icon.x-mark class="size-4" />
            </button>
        </div>
    </template>
</div>

{{-- Session Flash Messages --}}
@if(session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info') || session()->has('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.notify.success('{{ addslashes(session('success')) }}');
            @endif
            
            @if(session('error'))
                window.notify.error('{{ addslashes(session('error')) }}');
            @endif
            
            @if(session('warning'))
                window.notify.warning('{{ addslashes(session('warning')) }}');
            @endif
            
            @if(session('info'))
                window.notify.info('{{ addslashes(session('info')) }}');
            @endif
            
            @if(session('message'))
                window.notify.success('{{ addslashes(session('message')) }}');
            @endif
        });
    </script>
@endif
