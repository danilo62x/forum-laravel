@props([
    'color' => 'brand',
    'variant' => 'soft',
    'icon' => null,
    'dot' => false,
])
@php($soft = [
    'brand' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
    'success' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
    'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500',
    'error' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
    'gray' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
])
@php($solid = [
    'brand' => 'bg-brand-500 text-white',
    'success' => 'bg-success-500 text-white',
    'warning' => 'bg-warning-500 text-white',
    'error' => 'bg-error-500 text-white',
    'gray' => 'bg-gray-500 text-white',
])
@php($dots = [
    'brand' => 'bg-brand-500',
    'success' => 'bg-success-500',
    'warning' => 'bg-warning-500',
    'error' => 'bg-error-500',
    'gray' => 'bg-gray-400',
])
{{-- Mirrors wiki/react ui/Badge.tsx --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ' . ($variant === 'solid' ? ($solid[$color] ?? $solid['brand']) : ($soft[$color] ?? $soft['brand']))]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $variant === 'solid' ? 'bg-white' : ($dots[$color] ?? $dots['brand']) }}"></span>
    @endif
    @if ($icon)
        <x-icon name="{{ $icon }}" :size="12" :strokeWidth="2.2" />
    @endif
    {{ $slot }}
</span>
