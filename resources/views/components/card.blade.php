@props([
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'headerAction' => null,
    'footer' => null,
    'variant' => 'white',
])

@php
    $bgClass = match ($variant) {
        'amber' => 'bg-amber-100',
        'paper' => 'bg-stone-50',
        default => 'bg-white',
    };
@endphp

<div {{ $attributes->merge(['class' => "{$bgClass} border-2 border-slate-900 rounded-2xl shadow-[4px_4px_0px_#18181b] overflow-hidden transition duration-150"]) }}>
    @if ($title || $badge || $headerAction)
        <div class="px-5 py-4 border-b-2 border-slate-900 flex flex-wrap items-center justify-between gap-3 bg-white/70">
            <div>
                @if ($badge)
                    <div class="mb-1.5">{!! $badge !!}</div>
                @endif
                @if ($title)
                    <h3 class="font-heading font-extrabold text-slate-900 text-lg tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-slate-600 mt-0.5 font-medium">{{ $subtitle }}</p>
                @endif
            </div>

            @if ($headerAction)
                <div class="shrink-0">{!! $headerAction !!}</div>
            @endif
        </div>
    @endif

    <div class="p-5 sm:p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-5 py-3.5 border-t-2 border-slate-900 bg-slate-50/80 flex items-center justify-between gap-3 text-xs">
            {!! $footer !!}
        </div>
    @endif
</div>
