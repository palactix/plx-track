<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="">

    <div class="min-h-screen bg-background relative overflow-hidden">
        {{-- Colorful animated background elements --}}
        <div class="absolute inset-0 overflow-hidden">
            {{-- Main geometric shapes with colors --}}
            <div
                class="absolute top-20 right-20 w-96 h-96 rounded-full blur-3xl animate-pulse opacity-80
      bg-gradient-to-br from-purple-200/40 to-blue-200/50 dark:from-purple-500/20 dark:to-blue-500/30">
            </div>
            <div
                class="absolute bottom-20 left-20 w-80 h-80 rounded-full blur-3xl animate-pulse delay-1000 opacity-60
      bg-gradient-to-tr from-cyan-200/50 to-purple-200/40 dark:from-cyan-500/25 dark:to-purple-500/20">
            </div>
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-3xl animate-pulse delay-500 opacity-40
      bg-gradient-to-r from-blue-200/30 to-cyan-200/40 dark:from-blue-500/15 dark:to-cyan-500/20">
            </div>

            {{-- Grid pattern overlay --}}
            <div class="absolute inset-0 opacity-30">
                <div class="absolute inset-0"
                    style="background-image: radial-gradient(circle at 25px 25px, rgba(0,0,0,0.05) 2px, transparent 0); background-size: 50px 50px"
                    class="dark:[background-image:radial-gradient(circle_at_25px_25px,rgba(255,255,255,0.05)_2px,transparent_0)]">
                </div>
            </div>

            {{-- Floating glass panels with colors --}}
            <div
                class="absolute top-1/4 right-1/4 w-32 h-32 rounded-2xl backdrop-blur-sm border animate-pulse delay-700 opacity-50
      bg-gradient-to-br from-purple-200/30 to-transparent border-purple-300/30 dark:from-purple-500/10 dark:to-transparent dark:border-purple-400/20">
            </div>
            <div
                class="absolute bottom-1/3 left-1/3 w-24 h-24 rounded-xl backdrop-blur-sm border animate-pulse delay-300 opacity-40
      bg-gradient-to-tl from-cyan-200/40 to-transparent border-cyan-300/35 dark:from-cyan-500/15 dark:to-transparent dark:border-cyan-400/25">
            </div>
        </div>

		
        @include('partials.header')
		
		<main class="relative z-10 max-w-4xl mx-auto px-6 py-16">
        	@yield('content')
            {{ $slot }}
		</main>
        @include('partials.footer')
    </div>

</body>

</html>
