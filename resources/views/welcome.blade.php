@extends('layouts.public')

@section("head")
<!-- Functional Meta for SEO -->
<title>plx.bz — Free Open Source Link Shortener & Analytics</title>
<meta name="description" content="Shorten, share, and analyze links instantly with plx.bz. Free and open-source forever. Public analytics, no signup required, part of the Palactix ecosystem.">

<!-- Futuristic Meta for Social -->
<meta property="og:title" content="plx.bz — The Future of Open Link Sharing">
<meta property="og:description" content="Beyond shortening. plx.bz is open-source, transparent, and built for the web of tomorrow. Your links, your freedom.">
<meta property="og:url" content="https://plx.bz">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ asset('images/plx.bz-meta-image.png') }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="plx.bz — The Future of Open Link Sharing">
<meta name="twitter:description" content="Beyond shortening. plx.bz is open-source, transparent, and built for the web of tomorrow. Your links, your freedom.">
<meta name="twitter:image" content="{{ asset('images/plx.bz-meta-image.png') }}">


@endsection
@section('content')
    {{-- Hero Section --}}
    <div class="mb-16">
        <div class="text-center mb-16">
            <div
                class="inline-block p-1 bg-gradient-to-r from-purple-500 to-cyan-500 rounded-full mb-6 backdrop-blur-sm border border-purple-500/20 shadow-xl">
                <div class="bg-background/90 rounded-full px-6 py-2 backdrop-blur-md">
                    <span class="bg-clip-text bg-gradient-to-r from-purple-500 to-cyan-500">
                        ✨ Shorten. Share. Track
                    </span>
                </div>
            </div>
        

            <h1 class="text-5xl font-bold text-foreground mb-4 leading-tight">
                Open Source Link Shortener
                <br />
                <a href="https://palactix.com" class="text-transparent bg-clip-text bg-gradient-to-r from-purple-500 via-blue-500 to-cyan-500">
                    by Palactix
                </a>
            </h1>
        
            <p class="text-xl text-muted-foreground mb-12 max-w-2xl mx-auto">
                Create, Share, and Track Short links - Free & Open for All
            </p>
        </div>

        {{-- URL Input Section --}}
        <x-public.card>
            <livewire:link-generator />
        </x-public.card>

        {{-- Features --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-16">
            @foreach (['No Sign-up Required', 'Customizable Short URLs',  '100% Open Source (MIT)', 'Part of Palictix Ecosystem', 'Built-in Public Analytics', 'Developer Friendly'] as $feature)
                <div
                    class="flex items-center gap-3 backdrop-blur-lg bg-card/40 border border-border/30 rounded-xl p-4 hover:bg-card/60 transition-all duration-300 hover:scale-105 group
          shadow-[0_8px_32px_rgba(0,0,0,0.1)] hover:shadow-[0_12px_40px_rgba(147,51,234,0.2)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.2)] dark:hover:shadow-[0_12px_40px_rgba(147,51,234,0.3)] dark:border-gray-700">
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300">
                        {{-- <x-lucide-check class="w-4 h-4 text-white" /> --}}
                        ✔
                    </div>
                    <span
                        class="text-foreground group-hover:text-foreground/90 transition-colors duration-300">{{ $feature }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Public Short Links --}}
    <div
        class="backdrop-blur-xl bg-card/60 border-border/30 p-6 mb-8 transition-all duration-500 hover:bg-card/70
      shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)]">
        <livewire:public-links-table />
    </div>

    {{-- Call to Action --}}
    <div
        class="backdrop-blur-xl bg-gradient-to-r from-purple-500/20 via-blue-500/10 to-cyan-500/20 border-purple-500/30 p-8 text-center transition-all duration-500 hover:scale-[1.02]
      shadow-[0_20px_60px_rgba(147,51,234,0.2)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.3)]">
        <div class="flex items-center justify-center gap-3 mb-4">
            <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-cyan-500/20 shadow-inner">
                {{-- <x-lucide-bar-chart-3 class="w-8 h-8 text-cyan-500" /> --}}
            </div>
            <h3 class="text-2xl font-bold text-foreground">Want Full Analytics Access?</h3>
        </div>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">
            Get detailed insights, custom domains, and advanced features with a free account.
        </p>
        <button
            class="h-9 has-[>svg]:px-3 bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white border-0 px-8 transition-all duration-300 hover:scale-105 rounded-md
        shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)]">
            Sign Up Free
            {{-- <x-lucide-arrow-right class="w-4 h-4 ml-2" /> --}}
        </button>
    </div>
@endsection
