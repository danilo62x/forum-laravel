@props([
    'member' => 'member',
    'compact' => false,
])
{{-- Mirrors forum/react fo/MemberCard.tsx --}}
@if ($compact)
    <a x-data="{ get member() { return {{ $member }} } }" :href="'/membro/' + member.slug"
        class="flex items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-gray-50 dark:hover:bg-white/5">
        <x-ui.avatar src="member.avatar" name="member.name" size="sm" />
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-heading" x-text="member.name"></p>
            <p class="truncate text-xs text-muted" x-text="$t('member.reputation', { value: $number(member.reputation) })"></p>
        </div>
        <x-icon name="chevron-right" :size="16" class="text-gray-400" />
    </a>
@else
    <a x-data="{ get member() { return {{ $member }} }, get muted() { return foHelpers.isMuted({{ $member }}) } }"
        :href="'/membro/' + member.slug"
        class="surface-card flex h-full flex-col items-center rounded-2xl border p-5 text-center transition hover:-translate-y-0.5 hover:shadow-theme-md">
        <x-ui.avatar src="member.avatar" name="member.name" size="lg" />
        <p class="mt-3 w-full truncate text-sm font-semibold text-heading" x-text="member.name"></p>
        <div class="mt-2 flex flex-wrap items-center justify-center gap-1.5">
            <x-fo.role-pill role="member.role" />
            <template x-if="member.banned"><x-fo.banned-pill /></template>
            <template x-if="!member.banned && muted && member.mutedUntil"><x-fo.muted-pill until="member.mutedUntil" /></template>
        </div>
        <p class="mt-3 text-lg font-bold text-brand-500" x-text="$number(member.reputation)"></p>
        <p class="text-[11px] tracking-wide text-muted uppercase" x-text="$t('member.reputationLabel')"></p>
        <div class="mt-3 flex flex-wrap justify-center gap-1.5">
            <template x-for="badge in member.badges.slice(0, 3)" :key="badge">
                <x-fo.badge-pill name="badge" size="sm" />
            </template>
        </div>
        <div class="mt-auto flex w-full items-center justify-center gap-4 border-t border-themed pt-4 text-xs text-muted">
            <span class="flex items-center gap-1">
                <x-icon name="message-square" :size="13" />
                <span x-text="member.topicCount"></span>
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="message-circle" :size="13" />
                <span x-text="member.replyCount"></span>
            </span>
        </div>
    </a>
@endif
