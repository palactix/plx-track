{{-- Universal Confirmation Modal --}}
<div 
    x-data="confirmationModal"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    style="display: none;"
    @open-modal.window="openModal($event.detail)"
    @close-modal.window="closeModal()"
    @keydown.escape.window="closeModal()"
>
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 w-full max-w-md shadow-xl"
        @click.stop
    >
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-500/10 dark:bg-red-500/20 border border-red-500/20 dark:border-red-500/30 flex items-center justify-center">
                <flux:icon.exclamation-triangle class="size-6 text-red-500" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100" x-text="title">Confirm Action</h3>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm">This action cannot be undone.</p>
            </div>
        </div>
        
        {{-- Message --}}
        <p class="text-zinc-700 dark:text-zinc-300 mb-6" x-text="message">
            Are you sure you want to proceed?
        </p>
        
        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <flux:button 
                variant="outline" 
                @click="closeModal()"
            >
                Cancel
            </flux:button>
            
            <flux:button 
                variant="danger"
                @click="confirm()"
                x-text="confirmText"
            >
                Confirm
            </flux:button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('confirmationModal', () => ({
        isOpen: false,
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        confirmAction: null,
        
        openModal(data) {
            this.title = data.title || 'Confirm Action';
            this.message = data.message || 'Are you sure you want to proceed?';
            this.confirmText = data.confirmText || 'Confirm';
            this.confirmAction = data.confirmAction;
            this.isOpen = true;
        },
        
        closeModal() {
            this.isOpen = false;
            this.confirmAction = null;
        },
        
        confirm() {
            if (this.confirmAction && typeof this.confirmAction === 'function') {
                this.confirmAction();
            }
            this.closeModal();
        }
    }));
});
</script>
