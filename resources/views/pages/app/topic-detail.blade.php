<x-layout.app>
    @php($actionBtn = 'inline-flex items-center gap-1.5 rounded-lg border border-themed px-3 py-2 text-xs font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800')

    <div x-data="topicDetailPage({{ (int) $topicId }})">
        <template x-if="!topic">
            <div>
                <x-ui.empty-state icon="message-square" titleKey="topic.notFoundTitle" descKey="topic.notFoundDesc">
                    <x-slot:action>
                        <a href="{{ route('app.topics') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white"
                            x-text="$t('appNav.topics')"></a>
                    </x-slot:action>
                </x-ui.empty-state>
            </div>
        </template>

        <template x-if="topic">
        <div>
            <x-app.page-header title="topicDetail.title" subtitle="topicDetail.subtitle" :crumbs="[
                ['labelKey' => 'appNav.dashboard', 'href' => route('dashboard')],
                ['labelKey' => 'appNav.topics', 'href' => route('app.topics')],
                ['labelExpr' => 'topic ? topic.title : \'\''],
            ]">
                <x-slot:action>
                    <a :href="'/topico/' + topic.slug"
                        class="flex h-10 items-center gap-2 rounded-lg border border-themed px-4 text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                        <x-icon name="globe" :size="16" />
                        <span x-text="$t('topicDetail.viewPublic')"></span>
                    </a>
                </x-slot:action>
            </x-app.page-header>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="min-w-0 xl:col-span-2">
                    <article class="surface-card rounded-2xl border p-5 md:p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-fo.status-pill status="topic.status" />
                            <template x-if="topic.solved"><x-fo.solved-pill /></template>
                            <span x-show="category" class="text-xs text-muted" x-text="category ? category.name : ''"></span>
                        </div>
                        <h2 class="mt-3 text-xl font-bold text-heading" x-text="topic.title"></h2>
                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-muted">
                            <template x-if="author">
                                <span class="flex items-center gap-2">
                                    <x-ui.avatar src="author.avatar" name="author.name" size="xs" />
                                    <a :href="'/membro/' + author.slug" class="font-medium text-heading hover:text-brand-600" x-text="author.name"></a>
                                </span>
                            </template>
                            <span x-text="$datetime(topic.at)"></span>
                            <span class="flex items-center gap-1">
                                <x-icon name="eye" :size="13" />
                                <span x-text="$number(topic.views)"></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <x-icon name="trending-up" :size="13" />
                                <span x-text="$t('topic.score') + ' ' + foHelpers.score(topic)"></span>
                            </span>
                        </div>
                        <x-fo.rich-body text="topic.body" class="mt-4 min-w-0 break-words" />
                    </article>

                    <section class="mt-6">
                        <h3 class="text-base font-semibold text-heading" x-text="$t('topic.replies', { count: topic.replyCount })"></h3>
                        <p class="mt-1 text-xs text-muted" x-text="$t('topicDetail.repliesHint')"></p>
                        <div class="mt-4">
                            <x-fo.reply-tree topic="topic" :canModerate="true" canReply="true"
                                highlightId="highlight" onReply="onReply(%PARENT%)" />
                        </div>
                    </section>

                    <section id="resposta-da-equipe" class="surface-card mt-6 scroll-mt-24 rounded-2xl border p-5">
                        <h3 class="text-base font-semibold text-heading" x-text="$t('moderation.staffReplyTitle')"></h3>
                        <p class="mt-1 text-xs text-muted" x-text="$t('moderation.staffReplyDesc')"></p>
                        <div x-show="parentId !== null" x-cloak
                            class="mt-3 flex items-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-xs text-brand-700 dark:bg-brand-500/15 dark:text-brand-400">
                            <x-icon name="corner-down-right" :size="13" />
                            <span class="min-w-0 flex-1 truncate" x-text="$t('topic.replyingTo', { name: parentAuthorName })"></span>
                            <button type="button" @click="parentId = null" class="shrink-0 rounded-md p-1">
                                <x-icon name="x" :size="13" />
                            </button>
                        </div>
                        <textarea rows="4" x-model="staffReply" data-i18n-ph="moderation.staffReplyPlaceholder"
                            :placeholder="$t('moderation.staffReplyPlaceholder')"
                            class="focus-ring mt-3 w-full resize-y rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted"></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="button" :disabled="!staffReply.trim()" @click="sendStaffReply()"
                                class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600 disabled:opacity-50">
                                <x-icon name="send" :size="15" />
                                <span x-text="$t('moderation.staffReplySend')"></span>
                            </button>
                        </div>
                    </section>
                </div>

                <aside class="min-w-0 space-y-6">
                    <section class="surface-card rounded-2xl border p-5">
                        <h3 class="text-base font-semibold text-heading" x-text="$t('topicDetail.actionsTitle')"></h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" class="{{ $actionBtn }}" @click="$store.forum.togglePinned(topic.id)">
                                <x-icon name="pin" :size="14" />
                                <span x-text="topic.status === 'fixado' ? $t('moderation.unpin') : $t('moderation.pin')"></span>
                            </button>
                            <button type="button" class="{{ $actionBtn }}" @click="$store.forum.toggleClosed(topic.id)">
                                <template x-if="topic.status === 'fechado'"><x-icon name="unlock" :size="14" /></template>
                                <template x-if="topic.status !== 'fechado'"><x-icon name="lock" :size="14" /></template>
                                <span x-text="topic.status === 'fechado' ? $t('moderation.reopen') : $t('moderation.close')"></span>
                            </button>
                            <button type="button" class="{{ $actionBtn }}"
                                @click="$store.forum.setTopicStatus(topic.id, topic.status === 'oculto' ? 'aberto' : 'oculto')">
                                <template x-if="topic.status === 'oculto'"><x-icon name="eye" :size="14" /></template>
                                <template x-if="topic.status !== 'oculto'"><x-icon name="eye-off" :size="14" /></template>
                                <span x-text="topic.status === 'oculto' ? $t('moderation.approve') : $t('moderation.hide')"></span>
                            </button>
                            <button type="button" class="{{ $actionBtn }}" @click="moveTo = String(topic.categoryId); moveOpen = true">
                                <x-icon name="move" :size="14" />
                                <span x-text="$t('moderation.move')"></span>
                            </button>
                            <button type="button" class="{{ $actionBtn }} text-error-600 hover:text-error-700" @click="removeOpen = true">
                                <x-icon name="trash" :size="14" />
                                <span x-text="$t('common.remove')"></span>
                            </button>
                        </div>
                    </section>

                    <section class="surface-card rounded-2xl border p-5">
                        <h3 class="text-base font-semibold text-heading" x-text="$t('topic.aboutTitle')"></h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('common.status')"></dt>
                                <dd><x-fo.status-pill status="topic.status" /></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topic.score')"></dt>
                                <dd class="font-semibold text-heading" x-text="foHelpers.score(topic)"></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topic.replyTotal')"></dt>
                                <dd class="font-semibold text-heading" x-text="topic.replyCount"></dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted" x-text="$t('topicDetail.solved')"></dt>
                                <dd class="font-semibold text-heading" x-text="topic.solved ? $t('common.yes') : $t('common.no')"></dd>
                            </div>
                        </dl>
                    </section>
                </aside>
            </div>
        </div>
        </template>

        <x-ui.modal open="moveOpen" close="moveOpen = false" title="$t('moderation.moveTitle')" subtitle="topic ? topic.title : ''">
            <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('search.category')"></label>
            <select x-model="moveTo" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                <template x-for="c in $store.forum.categories" :key="c.id">
                    <option :value="String(c.id)" x-text="c.name"></option>
                </template>
            </select>
            <x-slot:footer>
                <button type="button" @click="moveOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doMove()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
                    x-text="$t('common.save')"></button>
            </x-slot:footer>
        </x-ui.modal>

        <x-ui.modal open="removeOpen" close="removeOpen = false" title="$t('moderation.removeTitle')" subtitle="topic ? topic.title : ''" size="sm">
            <p class="text-sm text-muted" x-text="$t('moderation.removeDesc')"></p>
            <x-slot:footer>
                <button type="button" @click="removeOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doRemove()" class="rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white hover:bg-error-600"
                    x-text="$t('common.remove')"></button>
            </x-slot:footer>
        </x-ui.modal>
    </div>

    @push('scripts')
        <script>
            function topicDetailPage(id) {
                return {
                    id,
                    highlight: null,
                    staffReply: '',
                    parentId: null,
                    moveOpen: false,
                    moveTo: '',
                    removeOpen: false,
                    get topic() { return $store.forum.topicById(this.id); },
                    get category() { return this.topic ? $store.forum.categoryById(this.topic.categoryId) : null; },
                    get author() { return this.topic ? $store.forum.memberById(this.topic.authorId) : null; },
                    get parentAuthorName() {
                        if (this.parentId === null) return '';
                        const reply = $store.forum.replyById(this.parentId);
                        const member = reply ? $store.forum.memberById(reply.authorId) : null;
                        return member ? member.name : '';
                    },
                    init() {
                        this.applyHash();
                        window.addEventListener('hashchange', () => this.applyHash());
                    },
                    applyHash() {
                        const hash = window.location.hash;
                        if (!hash.startsWith('#resposta-')) { this.highlight = null; return; }
                        const replyId = Number(hash.replace('#resposta-', ''));
                        if (!Number.isFinite(replyId)) return;
                        this.highlight = replyId;
                        setTimeout(() => {
                            const el = document.getElementById('resposta-' + replyId);
                            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }, 120);
                    },
                    onReply(pid) {
                        this.parentId = pid;
                        const el = document.getElementById('resposta-da-equipe');
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    },
                    sendStaffReply() {
                        const reply = $store.forum.addReply(this.topic.id, $store.forum.currentMemberId, this.staffReply, this.parentId);
                        if (!reply) return;
                        this.staffReply = '';
                        this.parentId = null;
                        $store.ui.notifySuccess($t('moderation.staffReplySent'));
                    },
                    doMove() {
                        $store.forum.moveTopic(this.topic.id, Number(this.moveTo));
                        this.moveOpen = false;
                        $store.ui.notifySuccess($t('moderation.moved'));
                    },
                    doRemove() {
                        $store.forum.removeTopic(this.topic.id);
                        $store.ui.notifySuccess($t('moderation.removed'));
                        window.location.href = '{{ route('app.topics') }}';
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
