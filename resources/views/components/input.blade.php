@props([
    'name',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'icon' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error || ($name && $errors->has($name));
    $errorMessage = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="block font-heading font-bold text-xs uppercase tracking-wider text-slate-900 mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-rose-600 font-black">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                {!! $icon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            value="{{ old($name, $value) }}"
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full rounded-xl border-2 ' . ($hasError ? 'border-rose-600 focus:border-rose-600 focus:shadow-[3px_3px_0px_#e11d48]' : 'border-slate-900 focus:border-amber-500 focus:shadow-[3px_3px_0px_#f59e0b]') . ' bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 placeholder:text-slate-400 shadow-[2px_2px_0px_#18181b] focus:outline-none focus:ring-0 transition duration-150 ' . ($icon ? 'pl-10' : '')
            ]) }}
        />
    </div>

    @if ($hint && !$hasError)
        <p class="mt-1.5 text-xs text-slate-500 font-medium">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 6.75h.008v.008H12v-.008Z" />
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @endif
</div>
