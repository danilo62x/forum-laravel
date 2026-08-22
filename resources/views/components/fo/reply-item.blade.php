@props([
    'reply' => 'reply',
    'topic' => 'topic',
    'depth' => 0,
    'canModerate' => false,
    'canReply' => 'false',
    'highlighted' => 'false',
    'onReply' => '',
    'onReport' => '',
])
@php($action = 'inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-muted transition hover:bg-gray-100 hover:text-heading dark:hover:bg-white/5')
{{-- Mirrors forum/react fo/ReplyItem.tsx --}}
<div x-data="{
        get reply() { return {{ $reply }} },
        get topic() { return {{ $topic }} },
        get author() { return $store.forum.memberById(this.reply.authorId) },
        get canReply() { return {{ $canReply }} },
        get highlighted() { return {{ $highlighted }} },
    }" class="contents">

    @unless ($canModerate)
        {{-- Resposta oculta some para quem não modera — troca de elemento, não
             de visibilidade, para a âncora #resposta-<id> nunca ficar duplicada. --}}
        <template x-if="reply.hidden">
            <div :id="'resposta-' + reply.id"
                class="scroll-mt-24 rounded-xl border border-dashed border-themed px-4 py-3 text-xs text-muted"
                x-text="$t('topic.hiddenReply')"></div>
        </template>
    @endunless

    <template x-if="{{ $canModerate ? 'true' : '!reply.hidden' }}">
    <div :id="'resposta-' + reply.id"
        class="surface-card scroll-mt-24 rounded-xl border p-4 transition"
        :class="[
            highlighted ? 'ring-2 ring-brand-400' : '',
            reply.best ? 'border-success-300 dark:border-success-500/40' : '',
            reply.hidden ? 'opacity-60' : '',
        ]">
        <p x-show="reply.best" x-cloak
            class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-success-50 px-2.5 py-1 text-xs font-semibold text-success-700 dark:bg-success-500/15 dark:text-success-500">
            <x-icon name="check-circle" :size="13" :strokeWidth="2.2" />
            <span x-text="$t('topic.bestReply')"></span>
        </p>
        @if ($canModerate)
            <p x-show="reply.hidden" x-cloak
                class="mb-3 inline-flex items-center gap-1.5 rounded-full bg-error-50 px-2.5 py-1 text-xs font-semibold text-error-600 dark:bg-error-500/15 dark:text-error-500">
                <x-icon name="eye-off" :size="13" :strokeWidth="2.2" />
                <span x-text="$t('topic.hiddenBadge')"></span>
            </p>
        @endif

        <div class="flex flex-wrap items-center gap-2">
            <template x-if="author">
                <a :href="'/membro/' + author.slug" class="flex min-w-0 items-center gap-2">
                    <x-ui.avatar src="author.avatar" name="author.name" size="{{ $depth === 0 ? 'sm' : 'xs' }}" />
                    <span class="truncate text-sm font-semibold text-heading" x-text="author.name"></span>
                </a>
            </template>
            <template x-if="author && author.role !== 'membro'">
                <x-fo.role-pill role="author.role" />
            </template>
            <span class="text-xs text-muted" :title="$datetime(reply.at)" x-text="$rel(reply.at)"></span>
            <span x-show="reply.edited" class="text-xs text-muted">· <span x-text="$t('topic.edited')"></span></span>
        </div>

        <x-fo.rich-body text="reply.body" class="mt-2 min-w-0 break-words" />

        <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-themed pt-3">
            <x-fo.vote-control score="foHelpers.score(reply)" vote="$store.forum.replyVote(reply.id)"
                onVote="$store.forum.voteReply(reply.id, %DIR%)" orientation="horizontal" size="sm" />

            @if ($onReply)
                @if ($depth === 0)
                    <button type="button" x-show="canReply" class="{{ $action }}"
                        @click="{{ str_replace('%PARENT%', 'reply.id', $onReply) }}">
                        <x-icon name="corner-down-right" :size="13" />
                        <span x-text="$t('topic.reply')"></span>
                    </button>
                @else
                    <button type="button" x-show="canReply" class="{{ $action }}"
                        @click="{{ str_replace('%PARENT%', '(reply.parentId ?? reply.id)', $onReply) }}">
                        <x-icon name="corner-down-right" :size="13" />
                        <span x-text="$t('topic.replyInThread')"></span>
                    </button>
                @endif
            @endif

            @if ($onReport && ! $canModerate)
                <button type="button" class="{{ $action }}" @click="{{ str_replace('%ID%', 'reply.id', $onReport) }}">
                    <x-icon name="flag" :size="13" />
                    <span x-text="$t('topic.report')"></span>
                </button>
            @endif

            @if ($canModerate)
                <div class="ml-auto flex flex-wrap items-center gap-1">
                    <button type="button" class="{{ $action }}" :title="$t('moderation.markBest')"
                        @click="$store.forum.markBest(topic.id, reply.id)">
                        <x-icon name="check-circle" :size="13" />
                        <span class="hidden sm:inline" x-text="$t('moderation.markBest')"></span>
                    </button>
                    <button type="button" class="{{ $action }}"
                        :title="reply.hidden ? $t('moderation.approve') : $t('moderation.hide')"
                        @click="$store.forum.setReplyHidden(reply.id, !reply.hidden)">
                        <template x-if="reply.hidden"><x-icon name="eye" :size="13" /></template>
                        <template x-if="!reply.hidden"><x-icon name="eye-off" :size="13" /></template>
                        <span class="hidden sm:inline" x-text="reply.hidden ? $t('moderation.approve') : $t('moderation.hide')"></span>
                    </button>
                    <button type="button" class="{{ $action }}" :title="$t('moderation.markSpam')"
                        @click="$store.forum.markSpam(reply.id, $store.forum.currentMemberId)">
                        <x-icon name="alert-triangle" :size="13" />
                        <span class="hidden sm:inline" x-text="$t('moderation.markSpam')"></span>
                    </button>
                    <button type="button" class="{{ $action }} text-error-500 hover:text-error-600"
                        :title="$t('common.remove')" @click="$store.forum.removeReply(reply.id)">
                        <x-icon name="trash" :size="13" />
                    </button>
                </div>
            @endif
        </div>
    </div>
    </template>
</div>
