@props([
    'status',
    'size' => 'md',
])

@php
    $statusValue = $status instanceof \App\Enums\StatusPesanan ? $status->value : (string) $status;

    $config = match ($statusValue) {
        'Lunas' => [
            'bg' => 'bg-emerald-100 text-emerald-950',
            'dot' => 'bg-emerald-600',
            'icon' => 'check',
            'label' => 'Lunas & Siap Berangkat',
        ],
        'Dalam Proses' => [
            'bg' => 'bg-amber-100 text-amber-950',
            'dot' => 'bg-amber-500 animate-pulse',
            'icon' => 'clock',
            'label' => 'Verifikasi Pembayaran',
        ],
        'Dibatalkan' => [
            'bg' => 'bg-rose-100 text-rose-950',
            'dot' => 'bg-rose-600',
            'icon' => 'x',
            'label' => 'Dibatalkan',
        ],
        default => [
            'bg' => 'bg-slate-100 text-slate-900',
            'dot' => 'bg-slate-600',
            'icon' => 'card',
            'label' => 'Menunggu Pembayaran',
        ],
    };

    $sizeClasses = $size === 'sm'
        ? 'px-2.5 py-0.5 text-[11px] gap-1.5 shadow-[1px_1px_0px_#18181b]'
        : 'px-3 py-1 text-xs gap-2 shadow-[1.5px_1.5px_0px_#18181b]';
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center font-heading font-extrabold uppercase tracking-wider rounded-full border-1.5 border-slate-900 select-none {$config['bg']} {$sizeClasses}"
]) }}>
    <span class="w-2 h-2 rounded-full shrink-0 {{ $config['dot'] }}"></span>
    <span>{{ $statusValue }}</span>
</span>
