@props([
    'checked' => 'false',
    'toggle' => '',
    'size' => 'md',
])
@php($sizeClass = $size === 'sm' ? 'h-5 w-9' : 'h-6 w-11')
@php($thumbSize = $size === 'sm' ? 'h-4 w-4' : 'h-5 w-5')
@php($activeTranslate = $size === 'sm' ? 'translate-x-4' : 'translate-x-5')
{{-- Mirrors wiki/react ui/SwitchToggle.tsx. $checked / $toggle are Alpine expressions. --}}
<button type="button" role="switch" :aria-checked="{{ $checked }}" @click="{{ $toggle }}"
    class="relative inline-flex shrink-0 items-center rounded-full transition {{ $sizeClass }}"
    :class="({{ $checked }}) ? 'bg-brand-500' : 'bg-gray-200 dark:bg-gray-700'">
    <span class="inline-block transform rounded-full bg-white shadow-theme-xs transition {{ $thumbSize }}"
        :class="({{ $checked }}) ? '{{ $activeTranslate }}' : 'translate-x-1'"></span>
</button>
