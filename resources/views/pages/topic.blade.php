<x-layout.public>
    <div x-data="topicPage(@js($slug))">
        {{-- Tópico inexistente --}}
        <template x-if="!topic">
            <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
                <x-ui.empty-state icon="message-square" titleKey="topic.notFoundTitle" descKey="topic.notFoundDesc">
                    <x-slot:action>
                        <a href="{{ route('home') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white"
                            x-text="$t('notFound.home')"></a>
                    </x-slot:action>
                </x-ui.empty-state>
            </div>
        </template>

        <template x-if="topic">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <x-ui.breadcrumb :items="[
                ['labelKey' => 'nav.home', 'href' => route('home'), 'icon' => 'grid'],
                ['labelExpr' => 'category ? category.name : \'\'', 'hrefExpr' => 'category ? \'/categoria/\' + category.slug : \'#\''],
                ['labelExpr' => 'topic ? topic.title : \'\''],
            ]" />

            <div class="mt-5 grid gap-8 lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    {{-- Post de abertura --}}
                    <article class="surface-card rounded-2xl border p-5 md:p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-fo.status-pill status="topic.status" />
                            <template x-if="topic.solved"><x-fo.solved-pill /></template>
                            <template x-if="category">
                                <a :href="'/categoria/' + category.slug"
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                    :style="{ backgroundColor: category.color + '1a', color: category.color }">
                                    <x-fo.category-icon icon="category.icon" :size="12" :strokeWidth="2.2" />
                                    <span x-text="category.name"></span>
                                </a>
                            </template>
                        </div>

                        <h1 class="mt-3 text-2xl font-bold text-heading" x-text="topic.title"></h1>

                        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted">
                            <template x-if="author">
                                <a :href="'/membro/' + author.slug" class="flex items-center gap-2 transition hover:text-brand-600">
                                    <x-ui.avatar src="author.avatar" name="author.name" size="sm" />
                                    <span class="font-semibold text-heading" x-text="author.name"></span>
                                </a>
                            </template>
                            <span :title="$datetime(topic.at)" x-text="$rel(topic.at)"></span>
                            <span class="flex items-center gap-1">
                                <x-icon name="eye" :size="13" />
                                <span x-text="$number(topic.views)"></span>
                            </span>
                        </div>

                        <div class="mt-4 flex gap-4">
                            <x-fo.vote-control score="foHelpers.score(topic)" vote="$store.forum.topicVote(topic.id)"
                                onVote="$store.forum.voteTopic(topic.id, %DIR%)" />
                            <x-fo.rich-body text="topic.body" class="min-w-0 flex-1 break-words" />
                        </div>

                        <div class="mt-4 flex flex-wrap gap-1.5" x-show="topic.tags.length > 0">
                            <template x-for="tag in topic.tags" :key="tag">
                                <a :href="'/busca?tag=' + encodeURIComponent(tag)"
                                    class="inline-flex items-center gap-1 rounded-md bg-gray-100 px-2 py-1 text-[11px] text-gray-600 transition hover:text-brand-600 dark:bg-gray-800 dark:text-gray-300">
                                    <x-icon name="hash" :size="10" :strokeWidth="2.4" />
                                    <span x-text="tag"></span>
                                </a>
                            </template>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-themed pt-4">
                            <button type="button" @click="openReport('topico', topic.id)"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="flag" :size="13" />
                                <span x-text="$t('topic.report')"></span>
                            </button>
                            <button type="button" @click="copyLink()"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-1.5 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="link" :size="13" />
                                <span x-text="$t('topic.copyLink')"></span>
                            </button>
                        </div>
                    </article>

                    {{-- Respostas --}}
                    <section class="mt-8">
                        <h2 class="text-lg font-bold text-heading" x-text="$t('topic.replies', { count: topic.replyCount })"></h2>
                        <div class="mt-4">
                            <x-fo.reply-tree topic="topic" canReply="Boolean($store.forum.me) && topic.status !== 'fechado'"
                                highlightId="highlight" onReply="onReply(%PARENT%)" onReport="openReport('resposta', %ID%)" />
                        </div>
                    </section>

                    <section id="caixa-de-resposta" class="mt-8 scroll-mt-24">
                        <h2 class="mb-3 text-lg font-bold text-heading" x-text="$t('topic.yourReply')"></h2>
                        <x-fo.composer-box topic="topic" author="$store.forum.me" />
                    </section>
                </div>

                <aside class="min-w-0 space-y-6">
                    <section class="surface-card rounded-2xl border p-5">
                        <h2 class="text-base font-semibold text-heading" x-text="$t('topic.aboutTitle')"></h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topic.score')"></dt>
                                <dd class="font-semibold text-heading" x-text="foHelpers.score(topic)"></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topic.replyTotal')"></dt>
                                <dd class="font-semibold text-heading" x-text="topic.replyCount"></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topic.views')"></dt>
                                <dd class="font-semibold text-heading" x-text="$number(topic.views)"></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topic.created')"></dt>
                                <dd class="font-semibold text-heading" x-text="$datetime(topic.at)"></dd>
                            </div>
                        </dl>
                    </section>

                    <section x-show="related.length > 0" class="surface-card rounded-2xl border p-5">
                        <h2 class="text-base font-semibold text-heading" x-text="$t('topic.relatedTitle')"></h2>
                        <ul class="mt-4 space-y-3">
                            <template x-for="item in related" :key="item.id">
                                <li>
                                    <a :href="'/topico/' + item.slug" class="block rounded-lg px-2 py-2 transition hover:bg-gray-50 dark:hover:bg-white/5">
                                        <p class="line-clamp-2 text-sm font-medium text-heading" x-text="item.title"></p>
                                        <p class="mt-1 text-xs text-muted"
                                            x-text="$t('topic.replies', { count: item.replyCount }) + ' · ' + $t('topic.score') + ' ' + foHelpers.score(item)"></p>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </section>
                </aside>
            </div>
        </div>
        </template>

        {{-- Denunciar --}}
        <x-ui.modal open="reportOpen" close="reportOpen = false" title="$t('report.title')" subtitle="$t('report.subtitle')">
            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('report.reason')"></label>
                    <select x-model="reason" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                        <template x-for="item in reasons" :key="item">
                            <option :value="item" x-text="$t('reason.' + item)"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('report.note')"></label>
                    <textarea rows="4" x-model="note" data-i18n-ph="report.notePlaceholder" :placeholder="$t('report.notePlaceholder')"
                        class="focus-ring w-full resize-y rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted"></textarea>
                    <p class="mt-1 text-xs text-muted" x-text="$t('report.noteHint')"></p>
                </div>
            </div>
            <x-slot:footer>
                <button type="button" @click="reportOpen = false"
                    class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body" x-text="$t('common.cancel')"></button>
                <button type="button" @click="sendReport()"
                    class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('report.send')"></button>
            </x-slot:footer>
        </x-ui.modal>
    </div>

    @push('scripts')
        <script>
            function topicPage(slug) {
                return {
                    slug,
                    highlight: null,
                    reportOpen: false,
                    reason: 'spam',
                    note: '',
                    reportTarget: null,
                    reasons: ['spam', 'ofensa', 'fora-do-tema', 'duplicado'],
                    get topic() { return $store.forum.topicBySlug(this.slug); },
                    get category() { return this.topic ? $store.forum.categoryById(this.topic.categoryId) : null; },
                    get author() { return this.topic ? $store.forum.memberById(this.topic.authorId) : null; },
                    get related() { return this.topic ? foHelpers.relatedTopics($store.forum.topics, this.topic, 4) : []; },
                    init() {
                        // Uma visita por abertura do tópico.
                        if (this.topic) $store.forum.registerView(this.topic.id);
                        this.applyHash();
                        window.addEventListener('hashchange', () => this.applyHash());
                    },
                    // Âncora #resposta-<id>: rola até a resposta e a destaca.
                    applyHash() {
                        const hash = window.location.hash;
                        if (!hash.startsWith('#resposta-')) { this.highlight = null; return; }
                        const id = Number(hash.replace('#resposta-', ''));
                        if (!Number.isFinite(id)) return;
                        this.highlight = id;
                        setTimeout(() => {
                            const el = document.getElementById('resposta-' + id);
                            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 120);
                    },
                    openReport(type, id) {
                        this.reportTarget = { type, id };
                        this.reason = 'spam';
                        this.note = '';
                        this.reportOpen = true;
                    },
                    sendReport() {
                        if (!this.reportTarget) return;
                        const me = $store.forum.me;
                        $store.forum.addReport(
                            this.reportTarget.type,
                            this.reportTarget.id,
                            me ? me.id : $store.forum.currentMemberId,
                            this.reason,
                            this.note.trim() || $t('report.noNote')
                        );
                        this.reportOpen = false;
                        $store.ui.notifySuccess($t('report.sent'));
                    },
                    onReply(parentId) {
                        $store.composer.setParent(this.topic.id, parentId);
                        const el = document.getElementById('caixa-de-resposta');
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    },
                    copyLink() {
                        if (navigator.clipboard) navigator.clipboard.writeText(window.location.href).catch(() => {});
                        $store.ui.notifySuccess($t('topic.linkCopied'));
                    },
                };
            }
        </script>
    @endpush
</x-layout.public>
