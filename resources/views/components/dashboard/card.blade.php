<div {{ $attributes->merge([
    'class' => 'backdrop-blur-xl bg-card/60 border border-purple-500/20 dark:border-gray-700 rounded-lg p-6 transition-all duration-500 hover:bg-card/70 hover:scale-[1.02] hover:border-purple-500/30 dark:hover:border-gray-600 shadow-[0_20px_60px_rgba(147,51,234,0.15)] hover:shadow-[0_20px_60px_rgba(147,51,234,0.25)] dark:shadow-[0_20px_60px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_60px_rgba(0,0,0,0.4)]'
]) }}>
    {{ $slot }}
</div>
