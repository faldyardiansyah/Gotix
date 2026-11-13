@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold tracking-tight transition-all duration-150 cursor-pointer select-none rounded-full border-2 border-slate-900 active:translate-y-0.5';

    $sizeClasses = match ($size) {
        'sm' => 'px-3.5 py-1.5 text-xs gap-1.5 shadow-[2px_2px_0px_#18181b] active:shadow-[1px_1px_0px_#18181b]',
        'lg' => 'px-7 py-3.5 text-base gap-3 shadow-[4px_4px_0px_#18181b] active:shadow-[1.5px_1.5px_0px_#18181b]',
        default => 'px-5 py-2.5 text-sm gap-2 shadow-[3px_3px_0px_#18181b] active:shadow-[1px_1px_0px_#18181b]',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-amber-500 text-slate-950 hover:bg-amber-400 hover:shadow-[4px_4px_0px_#18181b] hover:-translate-y-0.5',
        'secondary', 'outline' => 'bg-white text-slate-900 hover:bg-slate-50 hover:shadow-[4px_4px_0px_#18181b] hover:-translate-y-0.5',
        'danger' => 'bg-rose-500 text-white hover:bg-rose-600 hover:shadow-[4px_4px_0px_#18181b] hover:-translate-y-0.5',
        'dark' => 'bg-slate-900 text-white hover:bg-slate-800 hover:shadow-[4px_4px_0px_#f59e0b] hover:-translate-y-0.5',
        'ghost' => 'bg-transparent border-transparent shadow-none hover:bg-slate-200/60 text-slate-800',
        default => 'bg-amber-500 text-slate-950 hover:bg-amber-400 hover:shadow-[4px_4px_0px_#18181b] hover:-translate-y-0.5',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </button>
@endif
