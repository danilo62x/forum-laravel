@props(['reason' => "'spam'"])
{{-- Mirrors forum/react fo/StatusPill.tsx → ReasonPill --}}
<span x-data class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
    :class="foHelpers.badgeClass(foHelpers.reasonColor({{ $reason }}))"
    x-text="$t('reason.' + ({{ $reason }}))"></span>
