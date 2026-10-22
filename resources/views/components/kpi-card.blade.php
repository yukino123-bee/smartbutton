@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'blue',
    'valueId' => null,
    'hintId' => null,
    'urgent' => false,
])

@php
    $tones = [
        'red'   => ['icon' => 'bg-red-50 text-red-600 ring-red-100',       'bar' => 'bg-red-500',   'value' => 'text-red-600'],
        'amber' => ['icon' => 'bg-amber-50 text-amber-600 ring-amber-100', 'bar' => 'bg-amber-500', 'value' => 'text-slate-900'],
        'green' => ['icon' => 'bg-green-50 text-green-600 ring-green-100', 'bar' => 'bg-green-500', 'value' => 'text-slate-900'],
        'blue'  => ['icon' => 'bg-blue-50 text-blue-600 ring-blue-100',    'bar' => 'bg-blue-500',  'value' => 'text-slate-900'],
        'teal'  => ['icon' => 'bg-teal-50 text-teal-600 ring-teal-100',    'bar' => 'bg-teal-500',  'value' => 'text-slate-900'],
    ];
    $t = $tones[$tone] ?? $tones['blue'];
@endphp

<div {{ $attributes->class(['kpi-card', 'kpi-card-urgent' => $urgent]) }}>
    <span class="kpi-bar {{ $t['bar'] }}"></span>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="kpi-label">{{ $label }}</p>
            <p @if($valueId) id="{{ $valueId }}" @endif class="kpi-value {{ $t['value'] }}">{{ $value }}</p>
        </div>
        <div class="kpi-icon {{ $t['icon'] }}">{{ $slot }}</div>
    </div>
    @if($hint !== null)
        <p @if($hintId) id="{{ $hintId }}" @endif class="kpi-hint">{{ $hint }}</p>
    @endif
</div>
