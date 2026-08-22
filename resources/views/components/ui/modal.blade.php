@props([
    'open' => 'false',
    'close' => 'open = false',
    'title' => null,
    'subtitle' => null,
    'size' => 'md',
])
@php($sizes = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'])
{{-- Mirrors wiki/react ui/ModalBase.tsx. $open / $close / $title / $subtitle are Alpine expressions. --}}
<div x-show="{{ $open }}" x-cloak x-transition.opacity
    @keydown.escape.window="{{ $close }}"
    class="fixed inset-0 z-[80] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm">
    <div @click.outside="{{ $close }}"
        class="surface-card relative w-full overflow-hidden rounded-2xl border shadow-theme-xl {{ $sizes[$size] ?? $sizes['md'] }}">
        <header class="flex items-center justify-between border-b border-themed px-5 py-4">
            <div>
                <h3 class="text-base font-semibold text-heading" @if ($title) x-text="{{ $title }}" @endif></h3>
                @if ($subtitle)
                    <p class="mt-0.5 text-xs text-muted" x-text="{{ $subtitle }}"></p>
                @endif
            </div>
            <button type="button" aria-label="Fechar" @click="{{ $close }}"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <x-icon name="x" :size="20" />
            </button>
        </header>
        <div class="max-h-[70vh] overflow-y-auto px-5 py-5">{{ $slot }}</div>
        @isset($footer)
            <footer class="flex items-center justify-end gap-2 border-t border-themed px-5 py-3">{{ $footer }}</footer>
        @endisset
    </div>
</div>
