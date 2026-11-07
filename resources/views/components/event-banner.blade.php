@props([
    'event',
    'class' => '',
    'height' => 220,
])

@php
    $seed = (string) ($event->id ?? $event->name ?? 'event');
    $hash = abs(crc32($seed . 'banner'));
    $palettes = [
        ['#312e81', '#6366f1'],
        ['#0c4a6e', '#0ea5e9'],
        ['#4c1d95', '#a855f7'],
        ['#134e4a', '#14b8a6'],
        ['#7c2d12', '#f59e0b'],
        ['#831843', '#ec4899'],
        ['#1e3a8a', '#22d3ee'],
        ['#14532d', '#84cc16'],
    ];
    $palette = $palettes[$hash % count($palettes)];
    $gradientId = 'banner-gradient-' . substr(md5($seed), 0, 10);
    $patternId = 'banner-pattern-' . substr(md5($seed), 0, 10);
    $angle = $hash % 360;
@endphp

@if ($event->banner_url)
    <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}"
        {{ $attributes->class(['event-banner' => true, $class]) }}
        style="width: 100%; height: {{ $height }}px; object-fit: cover; display: block;">
@else
    <svg viewBox="0 0 800 260" preserveAspectRatio="xMidYMid slice" role="img"
        aria-label="Banner default {{ $event->name }}"
        {{ $attributes->class(['event-banner' => true, $class]) }}
        style="width: 100%; height: {{ $height }}px; display: block;">
        <defs>
            <linearGradient id="{{ $gradientId }}" x1="0%" y1="0%" x2="100%" y2="100%"
                gradientTransform="rotate({{ $angle }} .5 .5)">
                <stop offset="0%" stop-color="{{ $palette[0] }}" />
                <stop offset="100%" stop-color="{{ $palette[1] }}" />
            </linearGradient>
            <pattern id="{{ $patternId }}" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M0 40 L40 0" stroke="#ffffff" stroke-opacity="0.08" stroke-width="1" />
            </pattern>
        </defs>
        <rect width="800" height="260" fill="url(#{{ $gradientId }})" />
        <rect width="800" height="260" fill="url(#{{ $patternId }})" />
        <circle cx="700" cy="40" r="120" fill="#ffffff" fill-opacity="0.08" />
        <circle cx="90" cy="230" r="90" fill="#ffffff" fill-opacity="0.10" />
        <circle cx="620" cy="220" r="46" fill="#ffffff" fill-opacity="0.12" />
        <rect x="40" y="40" width="64" height="6" rx="3" fill="#ffffff" fill-opacity="0.55" />
        <rect x="40" y="56" width="34" height="6" rx="3" fill="#ffffff" fill-opacity="0.35" />
    </svg>
@endif
