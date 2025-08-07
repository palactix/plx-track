<div {{ $attributes->merge([
    'class' => 'backdrop-blur-xl bg-card/60 border-purple-500/20 p-8 mb-12 transition-all duration-500 hover:bg-card/70 hover:border-purple-500/30 shadow-[0_20px_60px_rgba(147,51,234,0.15)] dark:shadow-[0_20px_60px_rgba(147,51,234,0.2)]'
]) }}>
    {{ $slot }}
</div>