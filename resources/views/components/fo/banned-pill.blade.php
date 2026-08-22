{{-- Mirrors forum/react fo/StatusPill.tsx → BannedPill --}}
<span x-data class="inline-flex items-center gap-1.5 rounded-full bg-error-50 px-2.5 py-1 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
    <x-icon name="user-x" :size="12" :strokeWidth="2.2" />
    <span x-text="$t('members.banned')"></span>
</span>
