@extends('layouts.public')

@section('content')
  
    {{-- Link Info Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
      {{-- Short Link Card --}}
      <div class="backdrop-blur-xl bg-card/60 border-purple-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-purple-500/30 shadow-[0_20px_60px_rgba(147,51,234,0.15)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.2)]">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-foreground">Short Link:</h2>
          <div class="flex items-center gap-2 bg-green-500/10 rounded-full px-3 py-1 backdrop-blur-sm border border-green-500/20">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-green-500 text-sm font-medium">{{ $link->is_active ? 'Active' : 'Inactive' }}</span>
          </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-lg p-4 mb-4 border border-purple-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
          <code class="text-cyan-500 text-lg font-mono">{{ $link->short_code }}</code>
        </div>
        <div class="flex gap-2">
          <button type="button" 
                  onclick="copyToClipboard('{{ config('app.url') }}/{{ $link->short_code }}')" 
                  class="flex-1 bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)] px-4 py-2 text-sm font-medium">
            Copy Short URL
          </button>
          <a href="{{ config('app.url') }}/{{ $link->short_code }}" 
             target="_blank"
             class="bg-gray-500/20 hover:bg-gray-500/30 text-gray-300 hover:text-white transition-all duration-300 hover:scale-105 rounded-md px-4 py-2 text-sm font-medium">
            Visit
          </a>
        </div>

        <div class="bg-gradient-to-r from-blue-500/15 to-cyan-500/10 rounded-lg p-4 border border-blue-500/30 backdrop-blur-sm mt-5">
          <div class="flex items-center gap-2 mb-2">
            <span class="text-blue-500 font-medium">Analytics Period</span>
          </div>
          <p class="text-muted-foreground text-sm">
            Analytics available for last 7 days.
            <br />
            Created: {{ $link->created_at->format('M j, Y \a\t g:i A') }}
          </p>
        </div>

      </div>

      {{-- Original URL Card --}}
      <div class="backdrop-blur-xl bg-card/60 border-blue-500/20 p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-blue-500/30 shadow-[0_20px_60px_rgba(59,130,246,0.15)] dark:shadow-[0_20px_60px_rgba(59,130,246,0.2)]">
        <h2 class="text-xl font-bold text-foreground mb-4">Original URL:</h2>
        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-lg p-4 mb-4 border border-blue-500/20 backdrop-blur-sm shadow-[inset_0_4px_8px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_8px_rgba(0,0,0,0.2)]">
          <div class="flex items-center gap-2">
            <span class="text-muted-foreground text-sm break-all">{{ $link->original_url }}</span>
          </div>
          @if($link->title)
            <div class="mt-2 text-blue-400 font-medium">{{ $link->title }}</div>
          @endif
          @if($link->description)
            <div class="mt-1 text-muted-foreground text-sm">{{ $link->description }}</div>
          @endif
        </div>
        
      </div>
    </div>

    {{-- Analytics Chart --}}
    <div class="backdrop-blur-xl bg-card/60 border-cyan-500/20 p-6 mb-8 transition-all duration-500 hover:bg-card/70 shadow-[0_20px_60px_rgba(6,182,212,0.15)] dark:shadow-[0_20px_60px_rgba(6,182,212,0.2)]">
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-full bg-gradient-to-r from-cyan-500/20 to-blue-500/20 shadow-inner border border-cyan-500/30">
            📊
          </div>
          <h2 class="text-2xl font-bold text-foreground">Clicks per Day (Last 7 Days)</h2>
        </div>
        <div class="flex items-center gap-4">
          <div class="text-right backdrop-blur-sm bg-cyan-500/10 rounded-lg px-4 py-2 border border-cyan-500/30 shadow-[0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
            <div class="text-2xl font-bold text-cyan-500">{{ $link->clicks()->count() }}</div>
            <div class="text-muted-foreground text-sm">Total Clicks</div>
          </div>
          <div class="text-right backdrop-blur-sm bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-lg px-4 py-2 border border-green-500/30 shadow-[0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
            <div class="text-2xl font-bold text-green-500">{{ $link->clicks()->where('clicked_at', '>=', now()->subDays(7))->count() }}</div>
            <div class="text-muted-foreground text-sm">Last 7 Days</div>
          </div>
        </div>
      </div>
      {{-- Simple visualization of daily clicks --}}
      <div class="h-64 w-full backdrop-blur-sm bg-gradient-to-r from-cyan-500/5 to-purple-500/5 rounded-lg border border-cyan-500/20 p-4 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
        @php
          $dailyClicks = [];
          for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $clickCount = $link->clicks()->whereDate('clicked_at', $date)->count();
            $dailyClicks[] = [
              'date' => $date,
              'count' => $clickCount,
              'day' => $date->format('M j')
            ];
          }
          $maxClicks = max(array_column($dailyClicks, 'count')) ?: 1;
        @endphp
        
        <div class="flex items-end justify-between h-full gap-2">
          @foreach($dailyClicks as $day)
            <div class="flex-1 flex flex-col items-center h-full">
              <div class="flex-1 flex flex-col justify-end">
                <div class="bg-gradient-to-t from-cyan-500 to-purple-500 rounded-t-lg w-full transition-all duration-500 hover:scale-105"
                     style="height: {{ $maxClicks > 0 ? ($day['count'] / $maxClicks) * 100 : 0 }}%; min-height: {{ $day['count'] > 0 ? '8px' : '2px' }}">
                </div>
              </div>
              <div class="text-xs text-muted-foreground mt-2 text-center">
                <div class="font-medium">{{ $day['count'] }}</div>
                <div>{{ $day['day'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Detailed Analytics Table --}}
    <div class="backdrop-blur-xl bg-card/60 border-border/30 p-6 mb-8 transition-all duration-500 hover:bg-card/70 shadow-[0_20px_60px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)]">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-pink-500/20 shadow-inner border border-purple-500/30">
          🌐
        </div>
        <h2 class="text-2xl font-bold text-foreground">Recent Clicks (Last 7 Days)</h2>
      </div>
      <div class="overflow-x-auto backdrop-blur-sm bg-gradient-to-r from-purple-500/5 to-blue-500/5 rounded-lg border border-purple-500/20 shadow-[inset_0_4px_12px_rgba(0,0,0,0.1)] dark:shadow-[inset_0_4px_12px_rgba(0,0,0,0.2)]">
        @if($link->clicks->count() > 0)
          <table class="w-full">
            <thead>
              <tr class="border-b border-border/50">
                <th class="text-left py-3 px-4 text-muted-foreground">Date & Time</th>
                <th class="text-left py-3 px-4 text-muted-foreground">IP Address</th>
                <th class="text-left py-3 px-4 text-muted-foreground">User Agent</th>
                <th class="text-left py-3 px-4 text-muted-foreground">Referrer</th>
              </tr>
            </thead>
            <tbody>
              @foreach($link->clicks->take(20) as $click)
              <tr class="border-b border-border/30 hover:bg-purple-500/5 transition-all duration-300 group">
                <td class="py-4 px-4 text-foreground group-hover:text-foreground/90 transition-colors duration-300">
                  <div class="flex flex-col">
                    <span class="font-medium">{{ $click->clicked_at->format('M j, Y') }}</span>
                    <span class="text-sm text-muted-foreground">{{ $click->clicked_at->format('g:i A') }}</span>
                  </div>
                </td>
                <td class="py-4 px-4">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-500/20 to-cyan-500/20 flex items-center justify-center border border-blue-500/30 group-hover:scale-110 transition-transform duration-300 shadow-[0_2px_8px_rgba(0,0,0,0.1)] dark:shadow-[0_2px_8px_rgba(0,0,0,0.2)]">
                      <span class="text-blue-500 font-bold text-xs">IP</span>
                    </div>
                    <span class="text-foreground text-sm">{{ $click->ip_address ?? 'Unknown' }}</span>
                  </div>
                </td>
                <td class="py-4 px-4">
                  <div class="max-w-xs">
                    @php
                      $userAgent = $click->user_agent ?? 'Unknown';
                      $browser = 'Unknown Browser';
                      $platform = 'Unknown OS';
                      
                      if (str_contains($userAgent, 'Chrome')) $browser = 'Chrome';
                      elseif (str_contains($userAgent, 'Firefox')) $browser = 'Firefox';
                      elseif (str_contains($userAgent, 'Safari')) $browser = 'Safari';
                      elseif (str_contains($userAgent, 'Edge')) $browser = 'Edge';
                      
                      if (str_contains($userAgent, 'Windows')) $platform = 'Windows';
                      elseif (str_contains($userAgent, 'Macintosh')) $platform = 'macOS';
                      elseif (str_contains($userAgent, 'Linux')) $platform = 'Linux';
                      elseif (str_contains($userAgent, 'iPhone')) $platform = 'iOS';
                      elseif (str_contains($userAgent, 'Android')) $platform = 'Android';
                    @endphp
                    
                    <div class="flex items-center gap-2 mb-1">
                      <div class="flex items-center gap-2 bg-green-500/10 rounded-full px-2 py-1 border border-green-500/20">
                        <span class="text-green-500 text-xs">{{ $browser }}</span>
                      </div>
                      <div class="flex items-center gap-2 bg-blue-500/10 rounded-full px-2 py-1 border border-blue-500/20">
                        <span class="text-blue-500 text-xs">{{ $platform }}</span>
                      </div>
                    </div>
                    <div class="text-xs text-muted-foreground truncate" title="{{ $userAgent }}">
                      {{ Str::limit($userAgent, 50) }}
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4">
                  @if($click->referrer)
                    <div class="max-w-xs">
                      <div class="text-sm text-purple-500 font-medium">{{ parse_url($click->referrer, PHP_URL_HOST) }}</div>
                      <div class="text-xs text-muted-foreground truncate" title="{{ $click->referrer }}">
                        {{ Str::limit($click->referrer, 40) }}
                      </div>
                    </div>
                  @else
                    <span class="text-muted-foreground text-sm">Direct</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-500/10 flex items-center justify-center">
              <span class="text-2xl">📊</span>
            </div>
            <h3 class="text-lg font-medium text-foreground mb-2">No clicks yet</h3>
            <p class="text-muted-foreground">
              Share your short link to start collecting analytics data.
            </p>
          </div>
        @endif
      </div>
    </div>

    {{-- Navigation and Actions --}}
    <div class="backdrop-blur-xl bg-gradient-to-r from-purple-500/20 via-blue-500/10 to-cyan-500/20 border-purple-500/30 p-8 text-center transition-all duration-500 hover:scale-[1.02] shadow-[0_20px_60px_rgba(147,51,234,0.2)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.3)]">
      <div class="flex items-center justify-center gap-3 mb-4">
        <div class="p-2 rounded-full bg-gradient-to-r from-purple-500/20 to-cyan-500/20 shadow-inner border border-purple-500/30">
          🏠
        </div>
        <h3 class="text-2xl font-bold text-foreground">Want to create more short links?</h3>
      </div>
      <p class="text-muted-foreground mb-6 max-w-md mx-auto">
        Go back to the homepage to create more short links or sign up for advanced features.
      </p>
      <div class="flex items-center justify-center gap-4">
        <a href="{{ route('home') }}" 
           class="bg-gradient-to-r from-purple-500 to-cyan-500 hover:from-purple-600 hover:to-cyan-600 text-white px-8 py-3 transition-all duration-300 hover:scale-105 rounded-md shadow-[0_8px_24px_rgba(147,51,234,0.3)] dark:shadow-[0_8px_24px_rgba(147,51,234,0.4)] font-medium">
          ← Back to Home
        </a>
        <button type="button" class="backdrop-blur-sm bg-background/50 border-purple-500/30 hover:bg-purple-500/10 transition-all duration-300 hover:scale-105 shadow-[0_4px_16px_rgba(0,0,0,0.1)] dark:shadow-[0_4px_16px_rgba(0,0,0,0.3)] border outline-none px-6 py-3 rounded-md font-medium">
          Share Analytics
        </button>
      </div>
    </div>
  
  {{-- Copy to Clipboard Script --}}
  <script>
    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(function() {
        // Show a temporary success message
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = '✓ Copied!';
        btn.classList.add('bg-green-500');
        setTimeout(() => {
          btn.textContent = originalText;
          btn.classList.remove('bg-green-500');
        }, 2000);
      });
    }
  </script>
  
@endsection