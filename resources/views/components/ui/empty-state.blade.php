@props([
    'icon' => 'package',
    'titleKey' => null,
    'title' => null,
    'descKey' => null,
])
{{-- Mirrors wiki/react ui/EmptyState.tsx. $titleKey / $descKey are i18n keys. --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 text-center']) }} x-data>
    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
        <x-icon name="{{ $icon }}" :size="28" />
    </span>
    @if ($titleKey)
        <h3 class="mt-5 text-lg font-semibold text-heading" data-i18n="{{ $titleKey }}" x-text="$t('{{ $titleKey }}')">{{ $title }}</h3>
    @elseif ($title)
        <h3 class="mt-5 text-lg font-semibold text-heading">{{ $title }}</h3>
    @endif
    @if ($descKey)
        <p class="mt-2 max-w-sm text-sm text-muted" data-i18n="{{ $descKey }}" x-text="$t('{{ $descKey }}')"></p>
    @endif
    @isset($action)
        <div class="mt-6">{{ $action }}</div>
    @endisset
</div>
