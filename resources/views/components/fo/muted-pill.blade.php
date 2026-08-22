@props(['until' => 'null'])
{{-- Mirrors forum/react fo/StatusPill.tsx → MutedPill --}}
<span x-data class="inline-flex items-center gap-1.5 rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-700 dark:bg-warning-500/15 dark:text-warning-500">
    <x-icon name="volume-x" :size="12" :strokeWidth="2.2" />
    <span x-text="$t('members.mutedFor', { count: foHelpers.mutedDays({{ $until }}) })"></span>
</span>
