<div {{ $attributes->merge([
    'class' => 'backdrop-blur-xl bg-card/60 border border-purple-500/20 dark:border-gray-700 rounded-lg p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-purple-500/30 dark:hover:border-gray-600 shadow-[0_20px_60px_rgba(147,51,234,0.15)] hover:shadow-[0_20px_60px_rgba(147,51,234,0.25)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_60px_rgba(0,0,0,0.4)]'
]) }}>
    <div class="flex items-center justify-between">
        <div>
            <div class="text-2xl font-bold {{ $textColor ?? 'text-purple-500' }}">{{ $value }}</div>
            <div class="text-sm text-muted-foreground">{{ $label }}</div>
        </div>
        <div class="p-3 rounded-full bg-gradient-to-r {{ $iconBg ?? 'from-purple-500/20 to-purple-500/30' }} border {{ $iconBorder ?? 'border-purple-500/30' }}">
            {{ $icon }}
        </div>
    </div>
</div>
