@props(['event'])

@php
    $typeKey = $event->event_type ?: \App\Enums\EventType::KOMPETISI;
    $option = \App\Enums\EventType::options()[$typeKey]
        ?? \App\Enums\EventType::options()[\App\Enums\EventType::KOMPETISI];
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill text-bg-'.$option['color'].' event-type-badge']) }}
    title="{{ $option['hint'] }}">
    <i class="bi {{ $option['icon'] }} me-1"></i>{{ $option['label'] }}
</span>
