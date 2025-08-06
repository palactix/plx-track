<div>
    @if($generatedLink)
        {{-- Success State - Show Generated Link --}}
        <div class="backdrop-blur-xl bg-emerald-500/10 border border-emerald-500/30 p-6 rounded-lg mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center">
                    <span class="text-white text-sm">✓</span>
                </div>
                <h3 class="text-lg font-semibold text-foreground">Short Link Generated!</h3>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-muted-foreground">Your Short URL:</label>
                    <div class="flex gap-2 mt-1">
                        <input 
                            type="text" 
                            value="{{ config('app.url') }}/{{ $generatedLink->short_code }}" 
                            readonly
                            class="flex-1 bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground"
                            id="generated-url"
                        >
                        <button 
                            onclick="copyToClipboard()"
                            class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded transition-colors"
                        >
                            Copy
                        </button>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button 
                        wire:click="createAnother"
                        class="bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white px-6 py-2 rounded transition-all duration-300"
                    >
                        Create Another
                    </button>
                    
                    @if($this->isPublicMode)
                        <a 
                            href="#" 
                            class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded transition-colors"
                        >
                            View Analytics
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Link Generation Form --}}
        <form wire:submit="generateLink" class="space-y-4">
            {{-- Main URL Input --}}
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input 
                        type="url" 
                        wire:model="longUrl" 
                        placeholder="Enter a long URL (e.g., https://example.com)" 
                        class="w-full backdrop-blur-md bg-background/60 border-purple-500/20 focus:bg-background/80 focus:border-purple-500/40 transition-all duration-300 shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)] rounded-md border px-3 py-2"
                        required
                    >
                    @error('longUrl') 
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
                
                <button 
                    type="submit"
                    class="bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white border-0 px-8 py-2 transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)] whitespace-nowrap"
                    wire:loading.attr="disabled"
                    wire:target="generateLink"
                >
                    <span wire:loading.remove wire:target="generateLink">Generate Short Link</span>
                    <span wire:loading wire:target="generateLink">
                        @if($fetchingMetadata)
                            Fetching metadata...
                        @else
                            Generating...
                        @endif
                    </span>
                </button>
            </div>

            {{-- Advanced Options Toggle (for public users) --}}
            @if($this->isPublicMode)
                <div class="text-center">
                    <button 
                        type="button"
                        wire:click="toggleAdvancedOptions"
                        class="text-purple-500 hover:text-purple-400 text-sm transition-colors"
                    >
                        {{ $showAdvancedOptions ? '▲ Hide Advanced Options' : '▼ Show Advanced Options' }}
                    </button>
                </div>
            @endif

            {{-- Advanced Options --}}
            @if($this->canShowAdvancedOptions)
                <div class="space-y-4 p-4 bg-background/30 rounded-lg border border-border/30">
                    <h4 class="text-sm font-medium text-foreground">Advanced Options</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Custom Alias --}}
                        <div>
                            <label class="block text-sm text-muted-foreground mb-1">Custom Alias (Optional)</label>
                            <input 
                                type="text" 
                                wire:model="customAlias" 
                                placeholder="e.g., my-custom-link"
                                class="w-full bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors"
                            >
                            @error('customAlias') 
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Password Protection --}}
                        <div>
                            <label class="block text-sm text-muted-foreground mb-1">Password Protection (Optional)</label>
                            <input 
                                type="password" 
                                wire:model="password" 
                                placeholder="Enter password"
                                class="w-full bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors"
                            >
                            @error('password') 
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div>
                            <label class="block text-sm text-muted-foreground mb-1">
                                Title (Optional)
                                <span class="text-xs text-purple-500">• Auto-detected if empty</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="title" 
                                placeholder="Give your link a title (or leave empty for auto-detection)"
                                class="w-full bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors"
                            >
                            @error('title') 
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Expiration Date --}}
                        <div>
                            <label class="block text-sm text-muted-foreground mb-1">Expiration Date (Optional)</label>
                            <input 
                                type="datetime-local" 
                                wire:model="expiresAt" 
                                class="w-full bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors"
                                min="{{ now()->format('Y-m-d\TH:i') }}"
                            >
                            @error('expiresAt') 
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">
                            Description (Optional)
                            <span class="text-xs text-purple-500">• Auto-detected if empty</span>
                        </label>
                        <textarea 
                            wire:model="description" 
                            placeholder="Add a description for your link (or leave empty for auto-detection)"
                            rows="2"
                            class="w-full bg-background/60 border border-border/30 rounded px-3 py-2 text-foreground focus:border-purple-500/40 transition-colors resize-none"
                        ></textarea>
                        @error('description') 
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Auto-metadata info --}}
                    <div class="bg-purple-500/10 border border-purple-500/20 rounded-lg p-3">
                        <p class="text-sm text-purple-700 dark:text-purple-300">
                            💡 <strong>Smart Detection:</strong> If you leave title or description empty, we'll automatically fetch them from the webpage's meta tags for better link previews.
                        </p>
                    </div>
                </div>
            @endif
        </form>
    @endif

    {{-- Session Claim Prompt for Public Users --}}
    @if($this->isPublicMode && !$generatedLink)
        <div class="mt-4 p-4 bg-cyan-500/10 border border-cyan-500/30 rounded-lg">
            <p class="text-sm text-cyan-700 dark:text-cyan-300">
                💡 <strong>Tip:</strong> Create an account to manage your links, view detailed analytics, and access advanced features.
                <a href="#" class="underline hover:no-underline">Sign up free</a>
            </p>
        </div>
    @endif
</div>

<script>
    function copyToClipboard() {
        const input = document.getElementById('generated-url');
        input.select();
        input.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
        
        // Show feedback
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        button.classList.add('bg-emerald-500', 'hover:bg-emerald-600');
        button.classList.remove('bg-purple-500', 'hover:bg-purple-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-emerald-500', 'hover:bg-emerald-600');
            button.classList.add('bg-purple-500', 'hover:bg-purple-600');
        }, 2000);
    }
</script>
