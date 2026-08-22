@props([
    'topic' => 'topic',
    'author' => '$store.forum.me',
])
{{-- Mirrors forum/react fo/ComposerBox.tsx.
     Caixa de resposta com rascunho por tópico (fo-composer). Some para quem
     está silenciado, banido ou quando o tópico está fechado — a regra é do
     fórum, não da tela. --}}
<div x-data="{
        get topic() { return {{ $topic }} },
        get author() { return {{ $author }} },
        get draft() { return $store.composer.draftOf(this.topic.id) },
        get parent() { return this.draft.parentId !== null ? $store.forum.replyById(this.draft.parentId) : null },
        get parentAuthor() { return this.parent ? $store.forum.memberById(this.parent.authorId) : null },
        get muted() { return foHelpers.isMuted(this.author) },
        submit() {
            const reply = $store.forum.addReply(this.topic.id, this.author.id, this.draft.body, this.draft.parentId);
            if (!reply) return;
            $store.composer.clearDraft(this.topic.id);
            $store.ui.notifySuccess($t('topic.replySent'));
        },
    }">
    {{-- Tópico fechado --}}
    <template x-if="topic.status === 'fechado'">
        <div class="surface-card flex items-start gap-3 rounded-xl border border-dashed p-4 text-sm text-muted">
            <x-icon name="lock" :size="18" class="mt-0.5 shrink-0" />
            <p class="min-w-0" x-text="$t('topic.closedNotice')"></p>
        </div>
    </template>

    {{-- Visitante não identificado --}}
    <template x-if="topic.status !== 'fechado' && !author">
        <div class="surface-card flex flex-col gap-3 rounded-xl border border-dashed p-4 text-sm text-muted sm:flex-row sm:items-center">
            <p class="min-w-0 flex-1" x-text="$t('topic.loginNotice')"></p>
            <a href="{{ route('login') }}"
                class="flex h-10 shrink-0 items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600"
                x-text="$t('nav.login')"></a>
        </div>
    </template>

    {{-- Banido --}}
    <template x-if="topic.status !== 'fechado' && author && author.banned">
        <div class="surface-card flex items-start gap-3 rounded-xl border border-dashed p-4 text-sm text-muted">
            <x-icon name="user-x" :size="18" class="mt-0.5 shrink-0" />
            <p class="min-w-0" x-text="$t('topic.bannedNotice')"></p>
        </div>
    </template>

    {{-- Silenciado: a caixa vira aviso --}}
    <template x-if="topic.status !== 'fechado' && author && !author.banned && muted && author.mutedUntil">
        <div class="surface-card flex items-start gap-3 rounded-xl border border-dashed p-4 text-sm text-muted">
            <x-icon name="volume-x" :size="18" class="mt-0.5 shrink-0" />
            <p class="min-w-0"
                x-text="$t('topic.mutedNotice', { time: $t('duration.days', { count: foHelpers.mutedDays(author.mutedUntil) }) })"></p>
        </div>
    </template>

    {{-- Caixa de resposta --}}
    <template x-if="topic.status !== 'fechado' && author && !author.banned && !muted">
        <div class="surface-card rounded-xl border p-4">
            <div class="flex items-center gap-3">
                <x-ui.avatar src="author.avatar" name="author.name" size="sm" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-heading" x-text="author.name"></p>
                    <p class="text-xs text-muted" x-text="$t('topic.composerHint')"></p>
                </div>
            </div>

            <div x-show="parent" x-cloak
                class="mt-3 flex items-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-xs text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                <x-icon name="corner-down-right" :size="13" />
                <span class="min-w-0 flex-1 truncate" x-text="$t('topic.replyingTo', { name: parentAuthor ? parentAuthor.name : '' })"></span>
                <button type="button" @click="$store.composer.setParent(topic.id, null)" :aria-label="$t('common.cancel')"
                    class="shrink-0 rounded-md p-1 hover:bg-white/40 dark:hover:bg-white/10">
                    <x-icon name="x" :size="13" />
                </button>
            </div>

            <textarea rows="5" :value="draft.body" @input="$store.composer.setBody(topic.id, $event.target.value)"
                data-i18n-ph="topic.composerPlaceholder" :placeholder="$t('topic.composerPlaceholder')"
                class="focus-ring mt-3 w-full resize-y rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted"></textarea>

            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-muted" x-text="draft.body.trim() ? $t('topic.draftSaved') : $t('topic.markdownHint')"></p>
                <div class="flex items-center gap-2">
                    <button type="button" x-show="draft.body.trim()" x-cloak @click="$store.composer.clearDraft(topic.id)"
                        class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800"
                        x-text="$t('topic.discardDraft')"></button>
                    <button type="button" :disabled="!draft.body.trim()" @click="submit()"
                        class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <x-icon name="send" :size="15" />
                        <span x-text="$t('topic.sendReply')"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
