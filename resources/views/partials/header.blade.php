<header
    class="relative z-10 backdrop-blur-xl border-b
    bg-card/40 border-border/30 shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div
            class="text-2xl font-bold text-foreground tracking-tight bg-gradient-to-r from-purple-500 to-cyan-500 bg-clip-text text-transparent">
            <a wire:navigate href="{{  route('home') }}">plx.bz</a>
        </div>
        <nav class="flex items-center space-x-8">
            <a href="#"
                class="text-muted-foreground hover:text-purple-500 transition-all duration-300 hover:scale-105">About</a>
            <a href="#"
                class="text-muted-foreground hover:text-purple-500 transition-all duration-300 hover:scale-105 flex items-center gap-2">
                {{-- <x-lucide-github class="w-4 h-4" /> --}}
                GitHub
            </a>
            <a href="#"
                class="text-muted-foreground hover:text-purple-500 transition-all duration-300 hover:scale-105 flex items-center gap-2">
                {{-- <x-lucide-file-text class="w-4 h-4" /> --}}
                Docs
            </a>
            <button type="button"
                class="text-muted-foreground hover:text-purple-500 hover:bg-purple-500/10 transition-all duration-300">
                {{-- <x-lucide-moon class="w-4 h-4 dark:hidden" /> --}}
                {{-- <x-lucide-sun class="w-4 h-4 hidden dark:inline" /> --}}
            </button>
            <button type="button"
                wire:navigate href="{{ route('login') }}"
                class="backdrop-blur-sm bg-background/50 border-purple-500/30 hover:bg-purple-500/10 transition-all duration-300 hover:scale-105
          shadow-[0_4px_16px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_16px_rgba(0,0,0,0.3)] border outline-none px-4 py-2 rounded-md flex items-center">
                {{-- <x-lucide-user class="w-4 h-4 mr-2" /> --}}
                Login
            </button>
        </nav>
    </div>
</header>
