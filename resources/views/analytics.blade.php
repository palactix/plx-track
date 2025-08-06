<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<?php
$links = [];
$theme = ''; // Default theme
?>

<body class="">
  <div class="min-h-screen bg-background relative overflow-hidden">
  {{-- Colorful animated background elements --}}
  <div class="absolute inset-0 overflow-hidden">
    <div class="absolute top-20 right-20 w-96 h-96 rounded-full blur-3xl animate-pulse opacity-80 bg-gradient-to-br from-purple-200/40 to-blue-200/50 dark:from-purple-500/20 dark:to-blue-500/30"></div>
    <div class="absolute bottom-20 left-20 w-80 h-80 rounded-full blur-3xl animate-pulse delay-1000 opacity-60 bg-gradient-to-tr from-cyan-200/50 to-purple-200/40 dark:from-cyan-500/25 dark:to-purple-500/20"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full blur-3xl animate-pulse delay-500 opacity-40 bg-gradient-to-r from-blue-200/30 to-cyan-200/40 dark:from-blue-500/15 dark:to-cyan-500/20"></div>
    {{-- Grid pattern overlay --}}
    <div class="absolute inset-0 opacity-30">
      <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25px 25px, rgba(0,0,0,0.05) 2px, transparent 0); background-size: 50px 50px" class="dark:[background-image:radial-gradient(circle_at_25px_25px,rgba(255,255,255,0.05)_2px,transparent_0)]"></div>
    </div>
    {{-- Floating glass panels with colors --}}
    <div class="absolute top-1/4 right-1/4 w-32 h-32 rounded-2xl backdrop-blur-sm border animate-pulse delay-700 opacity-50 bg-gradient-to-br from-purple-200/30 to-transparent border-purple-300/30 dark:from-purple-500/10 dark:to-transparent dark:border-purple-400/20"></div>
    <div class="absolute bottom-1/3 left-1/3 w-24 h-24 rounded-xl backdrop-blur-sm border animate-pulse delay-300 opacity-40 bg-gradient-to-tl from-cyan-200/40 to-transparent border-cyan-300/35 dark:from-cyan-500/15 dark:to-transparent dark:border-cyan-400/25"></div>
  </div>

  {{-- Header --}}
  <header class="relative z-10 backdrop-blur-xl border-b bg-card/40 border-border/30 shadow-[0_8px_32px_rgba(0,0,0,0.1)] dark:shadow-[0_8px_32px_rgba(0,0,0,0.3)]">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button type="button" class="text-muted-foreground hover:text-purple-500 hover:bg-purple-500/10 transition-all duration-300 p-2">
          {{-- <x-lucide-arrow-left class="w-5 h-5" /> --}}
        </button>
        <div class="text-2xl font-bold text-foreground tracking-tight bg-gradient-to-r from-purple-500 to-cyan-500 bg-clip-text text-transparent">
          plx.bz
        </div>
      </div>
      <nav class="flex items-center space-x-8">
        <a href="#" class="text-muted-foreground hover:text-purple-500 transition-all duration-300 hover:scale-105">Analytics</a>
        <a href="#" class="text-muted-foreground hover:text-purple-500 transition-all duration-300 hover:scale-105 flex items-center gap-2">
          {{-- <x-lucide-github class="w-4 h-4" /> --}}
          GitHub
        </a>
        <a href="#" class="text-muted-foreground hover:text-purple-500 transition-all duration-300 hover:scale-105 flex items-center gap-2">
          {{-- <x-lucide-file-text class="w-4 h-4" /> --}}
          Docs
        </a>
        <button type="button" class="text-muted-foreground hover:text-purple-500 hover:bg-purple-500/10 transition-all duration-300">
          {{-- <x-lucide-moon class="w-4 h-4 dark:hidden" /> --}}
          {{-- <x-lucide-sun class="w-4 h-4 hidden dark:inline" /> --}}
        </button>
        <button type="button" class="backdrop-blur-sm bg-background/50 border-purple-500/30 hover:bg-purple-500/10 transition-all duration-300 hover:scale-105 shadow-[0_4px_16px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_16px_rgba(0,0,0,0.3)] border outline-none px-4 py-2 rounded-md flex items-center">
          {{-- <x-lucide-user class="w-4 h-4 mr-2" /> --}}
          Login
        </button>
      </nav>
    </div>
  </header>

  {{-- Main Content --}}
  <main class="relative z-10 max-w-4xl mx-auto px-6 py-16">
    {{-- Link Info Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
      {{-- Short Link Card --}}
      <div class="backdrop-blur-xl bg-card/60 border-purple-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-purple-500/30 shadow-[0_20px_60px_rgba(147,51,234,0.15)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.2)]">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-foreground">Short Link:</h2>
          <div class="flex items-center gap-2 bg-green-500/10 rounded-full px-3 py-1 backdrop-blur-sm border border-green-500/20">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-green-500 text-sm font-medium">Active</span>
          </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-lg p-4 mb-4 border border-purple-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
          <code class="text-cyan-500 text-lg font-mono">plx.bc23</code>
        </div>
        <button type="button" class="w-full bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)]">
          {{-- <x-lucide-copy class="w-4 h-4 mr-2" /> --}}
          Copy Short URL
        </button>
      </div>

      {{-- Original URL Card --}}
      <div class="backdrop-blur-xl bg-card/60 border-blue-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-blue-500/30 shadow-[0_20px_60px_rgba(59,130,246,0.15)] dark:shadow-[0_20px_60px_rgba(59,130,246,0.2)]">
        <h2 class="text-xl font-bold text-foreground mb-4">Original URL:</h2>
        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-lg p-4 mb-4 border border-blue-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
          <div class="flex items-center gap-2">
            {{-- <x-lucide-external-link class="w-4 h-4 text-blue-400" /> --}}
            <span class="text-muted-foreground text-sm truncate">https://github.com/palactix/url-shortener-example-repository</span>
          </div>
        </div>
        <div class="bg-gradient-to-r from-blue-500/15 to-cyan-500/10 rounded-lg p-4 border border-blue-500/30 backdrop-blur-sm">
          <div class="flex items-center gap-2 mb-2">
            {{-- <x-lucide-calendar class="w-4 h-4 text-blue-500" /> --}}
            <span class="text-blue-500 font-medium">Analytics Period</span>
          </div>
          <p class="text-muted-foreground text-sm">
            Analytics available for last 7 days.
            <br />
            Log in to claim this link for full access.
          </p>
        </div>
      </div>
    </div>

    {{-- Analytics Chart --}}
    <div class="backdrop-blur-xl bg-card/60 border-cyan-500/20 p-6 mb-8 transition-all duration-500 hover:bg-card/70 shadow-[0_20px_60px_rgba(6,182,212,0.15)] dark:shadow-[0_20px_60px_rgba(6,182,212,0.2)]">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-full bg-gradient-to-r from-cyan-500/20 to-blue-500/20 shadow-inner border border-cyan-500/30">
            {{-- <x-lucide-mouse-pointer class="w-6 h-6 text-cyan-500" /> --}}
          </div>
          <h2 class="text-2xl font-bold text-foreground">Clicks per Day</h2>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right backdrop-blur-sm bg-cyan-500/10 rounded-lg px-4 py-2 border border-cyan-500/30 shadow-[0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
            <div class="text-2xl font-bold text-cyan-500">119</div>
            <div class="text-muted-foreground text-sm">Total Clicks</div>
          </div>
          <div class="text-right backdrop-blur-sm bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-lg px-4 py-2 border border-green-500/30 shadow-[0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
            <div class="text-2xl font-bold text-green-500">+12%</div>
            <div class="text-muted-foreground text-sm">vs Last Week</div>
          </div>
        </div>
      </div>
      {{-- Replace this with your chart component in Laravel --}}
      <div class="h-64 w-full backdrop-blur-sm bg-gradient-to-r from-cyan-500/5 to-purple-500/5 rounded-lg border border-cyan-500/20 p-4 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)] flex items-center justify-center text-muted-foreground">
        [Line Chart Placeholder]
      </div>
    </div>

    {{-- Detailed Analytics Table --}}
    <div class="backdrop-blur-xl bg-card/60 border-border/30 p-6 mb-8 transition-all duration-500 hover:bg-card/70 shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)]">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-pink-500/20 shadow-inner border border-purple-500/30">
          {{-- <x-lucide-globe class="w-6 h-6 text-purple-500" /> --}}
        </div>
        <h2 class="text-2xl font-bold text-foreground">Detailed Analytics</h2>
      </div>
      <div class="overflow-x-auto backdrop-blur-sm bg-gradient-to-r from-purple-500/5 to-blue-500/5 rounded-lg border border-purple-500/20 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
        <table class="w-full">
          <thead>
            <tr class="border-b border-border/50">
              <th class="text-left py-3 px-4 text-muted-foreground">Date</th>
              <th class="text-left py-3 px-4 text-muted-foreground">Click Count</th>
              <th class="text-left py-3 px-4 text-muted-foreground">Location / Browser</th>
              <th class="text-right py-3 px-4 text-muted-foreground">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach([
              ['date' => 'Aug 6, 2025', 'clicks' => 25, 'location' => 'United States', 'browser' => 'Chrome'],
              ['date' => 'Aug 5, 2025', 'clicks' => 18, 'location' => 'Canada', 'browser' => 'Safari'],
              ['date' => 'Aug 4, 2025', 'clicks' => 22, 'location' => 'United Kingdom', 'browser' => 'Firefox'],
              ['date' => 'Aug 3, 2025', 'clicks' => 15, 'location' => 'Germany', 'browser' => 'Chrome'],
              ['date' => 'Aug 2, 2025', 'clicks' => 8, 'location' => 'France', 'browser' => 'Edge'],
            ] as $item)
            <tr class="border-b border-border/30 hover:bg-purple-500/5 transition-all duration-300 group">
              <td class="py-4 px-4 text-foreground group-hover:text-foreground/90 transition-colors duration-300">{{ $item['date'] }}</td>
              <td class="py-4 px-4">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-purple-500/20 to-cyan-500/20 flex items-center justify-center border border-purple-500/30 group-hover:scale-110 transition-transform duration-300 shadow-[0_2px_8px_rgba(0,0,0,0.1)] dark:shadow-[0_2px_8px_rgba(0,0,0,0.2)]">
                    <span class="text-purple-500 font-bold text-sm">{{ $item['clicks'] }}</span>
                  </div>
                </div>
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center gap-3">
                  <div class="flex items-center gap-2 bg-blue-500/10 rounded-full px-3 py-1 border border-blue-500/20">
                    {{-- <x-lucide-globe class="w-4 h-4 text-blue-500" /> --}}
                    <span class="text-foreground text-sm">{{ $item['location'] }}</span>
                  </div>
                  <div class="flex items-center gap-2 bg-green-500/10 rounded-full px-3 py-1 border border-green-500/20">
                    {{-- <x-lucide-monitor class="w-4 h-4 text-green-500" /> --}}
                    <span class="text-muted-foreground text-sm">{{ $item['browser'] }}</span>
                  </div>
                </div>
              </td>
              <td class="py-4 px-4 text-right">
                <button type="button" class="text-purple-500 hover:text-purple-400 hover:bg-purple-500/10 transition-all duration-300 hover:scale-105 rounded-md px-3 py-1">
                  View Details
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Upgrade Call to Action --}}
    <div class="backdrop-blur-xl bg-gradient-to-r from-purple-500/20 via-blue-500/10 to-cyan-500/20 border-purple-500/30 p-8 text-center transition-all duration-500 hover:scale-[1.02] shadow-[0_20px_60px_rgba(147,51,234,0.2)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.3)]">
      <div class="flex items-center justify-center gap-3 mb-4">
        <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-cyan-500/20 shadow-inner border border-purple-500/30">
          {{-- <x-lucide-calendar class="w-8 h-8 text-purple-500" /> --}}
        </div>
        <h3 class="text-2xl font-bold text-foreground">Want lifetime analytics and filters?</h3>
      </div>
      <p class="text-muted-foreground mb-6 max-w-md mx-auto">
        Claim this link to unlock unlimited analytics, custom domains, and advanced filtering options.
      </p>
      <div class="flex items-center justify-center gap-4">
        <button type="button" class="bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white px-8 transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)]">
          Claim Link
        </button>
        <button type="button" class="backdrop-blur-sm bg-background/50 border-purple-500/30 hover:bg-purple-500/10 transition-all duration-300 hover:scale-105 shadow-[0_4px_16px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_16px_rgba(0,0,0,0.3)] border outline-none px-4 py-2 rounded-md">
          Learn More
        </button>
      </div>
    </div>
  </main>

  {{-- Footer --}}
  
</div>
</body>
</html>