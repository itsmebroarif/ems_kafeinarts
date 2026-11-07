@props([
    'event',
    'size' => 48,
    'rounded' => true,
    'class' => '',
])

@php
    $seed = (string) ($event->id ?? $event->name ?? 'event');
    $hash = abs(crc32($seed));
    $palettes = [
        ['#6366f1', '#8b5cf6'],
        ['#0ea5e9', '#22d3ee'],
        ['#f59e0b', '#ef4444'],
        ['#10b981', '#84cc16'],
        ['#ec4899', '#8b5cf6'],
        ['#14b8a6', '#0ea5e9'],
        ['#f43f5e', '#f59e0b'],
        ['#3b82f6', '#6366f1'],
    ];
    $palette = $palettes[$hash % count($palettes)];
    $gradientId = 'avatar-gradient-' . substr(md5($seed), 0, 10);

    $initials = strtoupper((string) preg_replace(
        '/[^A-Za-z0-9]/',
        '',
        mb_substr((string) ($event->name ?? 'Event'), 0, 2)
    ));

    if ($initials === '') {
        $initials = 'EV';
    }
@endphp

@if ($event->logo_url)
    <img src="{{ $event->logo_url }}" alt="Avatar {{ $event->name }}"
        {{ $attributes->class(['rounded-circle' => $rounded, 'event-avatar' => true, $class]) }}
        style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover;">
@else
    <svg viewBox="0 0 64 64" role="img" aria-label="Avatar default {{ $event->name }}"
        {{ $attributes->class(['event-avatar' => true, 'rounded-circle' => $rounded, $class]) }}
        style="width: {{ $size }}px; height: {{ $size }}px; display: block;">
        <defs>
            <linearGradient id="{{ $gradientId }}" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="{{ $palette[0] }}" />
                <stop offset="100%" stop-color="{{ $palette[1] }}" />
            </linearGradient>
        </defs>
        <rect width="64" height="64" rx="{{ $rounded ? 32 : 12 }}" fill="url(#{{ $gradientId }})" />
        <circle cx="52" cy="12" r="12" fill="#ffffff" fill-opacity="0.18" />
        <circle cx="10" cy="54" r="16" fill="#ffffff" fill-opacity="0.12" />
        <text x="32" y="33" text-anchor="middle" dominant-baseline="middle" fill="#ffffff"
            font-size="24" font-weight="700" font-family="'Plus Jakarta Sans', sans-serif">{{ $initials }}</text>
    </svg>
@endif
