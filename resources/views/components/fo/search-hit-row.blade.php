@props(['hit' => 'hit'])
{{-- Mirrors forum/react fo/SearchHitRow.tsx — trecho destacado e âncora até a resposta. --}}
<article x-data="{
        get hit() { return {{ $hit }} },
        get category() { return $store.forum.categoryById(this.hit.topic.categoryId) },
        get author() { return $store.forum.memberById(this.hit.reply ? this.hit.reply.authorId : this.hit.topic.authorId) },
        get href() {
            return this.hit.reply
                ? '/topico/' + this.hit.topic.slug + '#resposta-' + this.hit.reply.id
                : '/topico/' + this.hit.topic.slug;
        },
        get when() { return this.hit.reply ? this.hit.reply.at : this.hit.topic.at },
    }"
    class="surface-card rounded-xl border p-4 transition hover:border-brand-300 dark:hover:border-brand-500/40">
    <div class="flex flex-wrap items-center gap-2">
        <template x-if="category">
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                :style="{ backgroundColor: category.color + '1a', color: category.color }">
                <x-fo.category-icon icon="category.icon" :size="12" :strokeWidth="2.2" />
                <span x-text="category.name"></span>
            </span>
        </template>
        <template x-if="hit.topic.solved"><x-fo.solved-pill /></template>
        <template x-if="hit.reply">
            <span class="inline-flex items-center gap-1 text-xs text-muted">
                <x-icon name="corner-down-right" :size="12" />
                <span x-text="$t('search.inReply')"></span>
            </span>
        </template>
    </div>

    <h3 class="mt-2 text-base font-semibold text-heading">
        <a :href="href" class="transition hover:text-brand-600 dark:hover:text-brand-400" x-text="hit.topic.title"></a>
    </h3>

    <p class="mt-2 min-w-0 text-sm break-words text-muted">
        <span x-text="hit.snippet.before"></span><mark x-show="hit.snippet.match"
            class="rounded bg-warning-100 px-0.5 text-heading dark:bg-warning-500/25" x-text="hit.snippet.match"></mark><span
            x-text="hit.snippet.after"></span>
    </p>

    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted">
        <template x-if="author">
            <span class="flex items-center gap-2">
                <x-ui.avatar src="author.avatar" name="author.name" size="xs" />
                <span x-text="author.name"></span>
            </span>
        </template>
        <span class="flex items-center gap-1">
            <x-icon name="clock" :size="13" />
            <span x-text="$rel(when)"></span>
        </span>
        <span class="flex items-center gap-1">
            <x-icon name="message-circle" :size="13" />
            <span x-text="$t('topic.replies', { count: hit.topic.replyCount })"></span>
        </span>
    </div>
</article>
