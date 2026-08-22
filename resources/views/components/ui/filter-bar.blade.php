@props([
    'search' => null,
    'placeholderKey' => 'common.search',
])
{{-- Mirrors forum/react ui/FilterBar.tsx. $search é um caminho atribuível do x-data. --}}
<div class="surface-card mb-5 flex flex-col gap-3 rounded-xl border p-4 lg:flex-row lg:items-center" x-data>
    @if ($search)
        <div class="relative w-full lg:max-w-xs">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted">
                <x-icon name="search" :size="16" />
            </span>
            <input x-model="{{ $search }}" data-i18n-ph="{{ $placeholderKey }}" :placeholder="$t('{{ $placeholderKey }}')"
                class="focus-ring w-full rounded-lg border border-themed bg-transparent py-2 pr-3 pl-9 text-sm text-body" />
        </div>
    @endif
    <div class="flex flex-wrap items-center gap-3">{{ $slot }}</div>
    @isset($action)
        <div class="lg:ml-auto">{{ $action }}</div>
    @endisset
</div>
