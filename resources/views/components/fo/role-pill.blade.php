@props(['role' => "'membro'"])
{{-- Mirrors forum/react fo/StatusPill.tsx → RolePill --}}
<span x-data class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
    :class="foHelpers.badgeClass(foHelpers.roleColor({{ $role }}))">
    <template x-if="({{ $role }}) === 'membro'"><x-icon name="user" :size="12" :strokeWidth="2.2" /></template>
    <template x-if="({{ $role }}) !== 'membro'"><x-icon name="shield-check" :size="12" :strokeWidth="2.2" /></template>
    <span x-text="$t('role.' + ({{ $role }}))"></span>
</span>
