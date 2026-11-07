@props([
    'variant' => 'btn-outline-secondary',
    'label' => 'Ganti tema',
])

<button type="button" class="btn btn-sm {{ $variant }} theme-toggle" data-theme-toggle
    title="{{ $label }}" aria-label="{{ $label }}" {{ $attributes }}>
    <i class="bi bi-moon-stars theme-icon-moon"></i>
    <i class="bi bi-sun theme-icon-sun"></i>
</button>
