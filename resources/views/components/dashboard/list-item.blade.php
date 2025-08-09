<div {{ $attributes->merge([
    'class' => 'backdrop-blur-xl bg-card/60 border border-purple-500/20 dark:border-gray-700 rounded-lg p-4 transition-all duration-500 hover:bg-card/70 hover:scale-[1.01] hover:border-purple-500/30 dark:hover:border-gray-600 shadow-[0_8px_24px_rgba(147,51,234,0.1)] hover:shadow-[0_12px_32px_rgba(147,51,234,0.2)] dark:shadow-[0_8px_24px_rgba(0,0,0,0.2)] dark:hover:shadow-[0_12px_32px_rgba(0,0,0,0.3)] group'
]) }}>
    {{ $slot }}
</div>
