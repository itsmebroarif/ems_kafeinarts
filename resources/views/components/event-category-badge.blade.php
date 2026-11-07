@props([
    'event' => null,
    'category' => null,
    'size' => 'sm',
    'showEmpty' => false,
    'emptyLabel' => 'Umum',
])

@php
    $category = $category ?? ($event->category ?? null);
    $rgb = $category ? $category->rgb() : [108, 117, 125];
    $label = $category ? $category->name : $emptyLabel;
    $icon = $category ? 'bi-collection' : 'bi-tag';
    $sizeClass = $size === 'lg' ? 'fs-6 py-2 px-3' : 'py-1 px-2';
    $textColor = (0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2]) > 150
        ? '#212529'
        : '#fff';
@endphp

@if ($category || $showEmpty)
    <span class="badge rounded-pill fw-semibold {{ $sizeClass }}"
        style="background-color: rgb({{ $rgb[0] }}, {{ $rgb[1] }}, {{ $rgb[2] }}); color: {{ $textColor }};">
        <i class="bi {{ $icon }} me-1"></i>{{ $label }}
    </span>
@endif
