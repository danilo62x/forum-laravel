@props([
    'titleKey' => '',
    'value' => '0',
    'icon' => 'chart',
    'accent' => 'brand',
])
@php($bg = [
    'brand' => 'bg-brand-50 dark:bg-brand-500/15',
    'success' => 'bg-success-50 dark:bg-success-500/15',
    'warning' => 'bg-warning-50 dark:bg-warning-500/15',
    'error' => 'bg-error-50 dark:bg-error-500/15',
    'info' => 'bg-blue-light-50 dark:bg-blue-light-500/15',
])
@php($color = [
    'brand' => 'text-brand-500',
    'success' => 'text-success-600',
    'warning' => 'text-warning-600',
    'error' => 'text-error-500',
    'info' => 'text-blue-light-500',
])
{{-- Mirrors forum/react ui/StatsCard.tsx. $value é uma expressão Alpine. --}}
<div class="surface-card rounded-2xl border p-5 shadow-theme-sm md:p-6" x-data>
    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $bg[$accent] ?? $bg['brand'] }}">
        <x-icon :name="$icon" :size="22" class="{{ $color[$accent] ?? $color['brand'] }}" />
    </div>
    <div class="mt-5 flex items-end justify-between">
        <div>
            <span class="text-sm text-muted" data-i18n="{{ $titleKey }}" x-text="$t('{{ $titleKey }}')"></span>
            <h4 class="mt-2 font-bold text-heading text-title-sm" x-text="{{ $value }}"></h4>
        </div>
    </div>
</div>
