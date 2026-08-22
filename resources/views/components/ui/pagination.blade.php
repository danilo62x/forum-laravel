@props([
    'page' => 'page',
    'totalPages' => 'totalPages',
])
{{-- Mirrors forum/react ui/Pagination.tsx. `page` precisa ser um caminho
     atribuível do x-data que envolve o componente (ex.: `page`). --}}
<nav x-data x-show="({{ $totalPages }}) > 1" x-cloak class="flex items-center justify-center gap-2" aria-label="Pagination">
    <button type="button" aria-label="Previous" @click="{{ $page }} = Math.max(1, ({{ $page }}) - 1)"
        :disabled="({{ $page }}) <= 1"
        class="flex h-10 min-w-10 items-center justify-center rounded-lg border border-themed px-3 text-sm font-medium text-muted transition hover:text-heading disabled:opacity-40">
        <x-icon name="chevron-left" :size="16" />
    </button>
    <template x-for="(p, i) in foHelpers.pageNumbers({{ $page }}, {{ $totalPages }})" :key="i">
        <span class="flex items-center">
            <span x-show="p === '...'" class="px-1 text-muted">…</span>
            <button type="button" x-show="p !== '...'" @click="{{ $page }} = p" x-text="p"
                class="flex h-10 min-w-10 items-center justify-center rounded-lg border px-3 text-sm font-medium transition"
                :class="p === ({{ $page }}) ? 'border-brand-500 bg-brand-500 text-white' : 'border-themed text-body hover:text-heading'"></button>
        </span>
    </template>
    <button type="button" aria-label="Next" @click="{{ $page }} = Math.min(({{ $totalPages }}), ({{ $page }}) + 1)"
        :disabled="({{ $page }}) >= ({{ $totalPages }})"
        class="flex h-10 min-w-10 items-center justify-center rounded-lg border border-themed px-3 text-sm font-medium text-muted transition hover:text-heading disabled:opacity-40">
        <x-icon name="chevron-right" :size="16" />
    </button>
</nav>
