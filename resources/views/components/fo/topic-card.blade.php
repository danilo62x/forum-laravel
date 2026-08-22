@props([
    'topic' => 'topic',
    'showCategory' => true,
    'compact' => false,
])
{{-- Mirrors forum/react fo/TopicCard.tsx — linha de tópico usada na home, na
     categoria e nos blocos de destaque. --}}
<article x-data="{
        get topic() { return {{ $topic }} },
        get category() { return $store.forum.categoryById(this.topic.categoryId) },
        get author() { return $store.forum.memberById(this.topic.authorId) },
    }"
    class="surface-card flex gap-4 rounded-xl border p-4 transition hover:border-brand-300 dark:hover:border-brand-500/40">
    @unless ($compact)
        <x-fo.vote-control score="foHelpers.score(topic)" vote="$store.forum.topicVote(topic.id)"
            onVote="$store.forum.voteTopic(topic.id, %DIR%)" size="sm" />
    @endunless

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <template x-if="topic.status !== 'aberto'">
                <x-fo.status-pill status="topic.status" />
            </template>
            <template x-if="topic.solved"><x-fo.solved-pill /></template>
            @if ($showCategory)
                <template x-if="category">
                    <a :href="'/categoria/' + category.slug"
                        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                        :style="{ backgroundColor: category.color + '1a', color: category.color }">
                        <x-fo.category-icon icon="category.icon" :size="12" />
                        <span x-text="category.name"></span>
                    </a>
                </template>
            @endif
        </div>

        <h3 class="mt-2 text-base font-semibold text-heading">
            <a :href="'/topico/' + topic.slug" class="transition hover:text-brand-600 dark:hover:text-brand-400" x-text="topic.title"></a>
        </h3>

        <div class="mt-2 flex flex-wrap gap-1.5" x-show="topic.tags.length > 0">
            <template x-for="tag in topic.tags" :key="tag">
                <a :href="'/busca?tag=' + encodeURIComponent(tag)"
                    class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-0.5 text-[11px] text-gray-600 transition hover:text-brand-600 dark:bg-gray-800 dark:text-gray-300">
                    <x-icon name="hash" :size="10" :strokeWidth="2.4" />
                    <span x-text="tag"></span>
                </a>
            </template>
        </div>

        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted">
            <template x-if="author">
                <a :href="'/membro/' + author.slug" class="flex items-center gap-2 transition hover:text-brand-600">
                    <x-ui.avatar src="author.avatar" name="author.name" size="xs" />
                    <span class="font-medium" x-text="author.name"></span>
                </a>
            </template>
            <span class="flex items-center gap-1">
                <x-icon name="message-circle" :size="13" />
                <span x-text="$t('topic.replies', { count: topic.replyCount })"></span>
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="eye" :size="13" />
                <span x-text="$number(topic.views)"></span>
            </span>
            <span class="flex items-center gap-1">
                <x-icon name="clock" :size="13" />
                <span x-text="$t('topic.lastReply', { time: $rel(topic.lastReplyAt) })"></span>
            </span>
        </div>
    </div>
</article>
