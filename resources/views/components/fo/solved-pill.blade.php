{{-- Mirrors forum/react fo/StatusPill.tsx → SolvedPill --}}
<span x-data class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/15 dark:text-success-500">
    <x-icon name="check-circle" :size="12" :strokeWidth="2.2" />
    <span x-text="$t('status.solved')"></span>
</span>
