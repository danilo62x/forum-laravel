@props(['status' => "'aberto'"])
{{-- Mirrors forum/react fo/StatusPill.tsx → TopicStatusPill --}}
<span x-data class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
    :class="foHelpers.badgeClass(foHelpers.statusColor({{ $status }}))">
    <template x-if="foHelpers.statusIcon({{ $status }}) === 'message-circle'"><x-icon name="message-circle" :size="12" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.statusIcon({{ $status }}) === 'lock'"><x-icon name="lock" :size="12" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.statusIcon({{ $status }}) === 'pin'"><x-icon name="pin" :size="12" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.statusIcon({{ $status }}) === 'eye-off'"><x-icon name="eye-off" :size="12" :strokeWidth="2.2" /></template>
    <span x-text="$t('status.' + ({{ $status }}))"></span>
</span>
