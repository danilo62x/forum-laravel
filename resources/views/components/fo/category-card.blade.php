@props([
    'category' => 'category',
    'replyCount' => '0',
])
{{-- Mirrors forum/react fo/CategoryCard.tsx --}}
<a x-data="{
        get category() { return {{ $category }} },
        get replyCount() { return {{ $replyCount }} },
        get moderators() { return this.category.moderatorIds.map(id => $store.forum.memberById(id)).filter(Boolean) },
    }"
    :href="'/categoria/' + category.slug"
    class="surface-card group flex h-full flex-col rounded-2xl border p-5 transition hover:-translate-y-0.5 hover:shadow-theme-md">
    <div class="flex items-start gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
            :style="{ backgroundColor: category.color + '1a', color: category.color }">
            <x-fo.category-icon icon="category.icon" :size="22" />
        </span>
        <div class="min-w-0 flex-1">
            <h3 class="truncate text-base font-semibold text-heading transition group-hover:text-brand-600 dark:group-hover:text-brand-400"
                x-text="category.name"></h3>
            <p class="text-xs text-muted"
                x-text="$t('category.topicCount', { count: category.topicCount }) + ' · ' + $t('category.replyCount', { count: replyCount })"></p>
        </div>
    </div>

    <p class="mt-3 line-clamp-3 min-w-0 text-sm text-muted" x-text="category.description"></p>

    <div class="mt-4 flex items-center justify-between gap-3 border-t border-themed pt-4">
        <div class="flex min-w-0 items-center gap-2">
            <div class="flex -space-x-2">
                <template x-for="m in moderators.slice(0, 3)" :key="m.id">
                    <span class="rounded-full ring-2" style="--tw-ring-color: var(--tx-card-bg)">
                        <x-ui.avatar src="m.avatar" name="m.name" size="xs" />
                    </span>
                </template>
            </div>
            <span class="truncate text-[11px] text-muted" x-text="$t('category.moderatedBy')"></span>
        </div>
        <span x-show="category.staffOnly" class="flex shrink-0 items-center gap-1 text-[11px] text-muted">
            <x-icon name="lock" :size="12" />
            <span x-text="$t('category.staffOnly')"></span>
        </span>
    </div>
</a>
