<x-layout.app>
    @php($field = 'focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted')
    @php($iconBtn = 'flex h-8 w-8 items-center justify-center rounded-lg border border-themed text-muted transition hover:text-brand-500')

    <div x-data="topicsPage({{ $openNew ? 'true' : 'false' }})">
        <x-app.page-header title="topicsAdmin.title" subtitle="topicsAdmin.subtitle" :crumbs="[
            ['labelKey' => 'appNav.dashboard', 'href' => route('dashboard')],
            ['labelKey' => 'appNav.topics'],
        ]">
            <x-slot:action>
                <button type="button" @click="createOpen = true"
                    class="flex h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                    <x-icon name="plus" :size="16" />
                    <span x-text="$t('topicsAdmin.new')"></span>
                </button>
            </x-slot:action>
        </x-app.page-header>

        <x-ui.filter-bar search="search" placeholderKey="topicsAdmin.searchPlaceholder">
            <label class="flex items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('search.category')"></span>
                <select x-model="categoryFilter" @change="page = 1"
                    class="focus-ring rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <template x-for="c in $store.forum.categories" :key="c.id">
                        <option :value="String(c.id)" x-text="c.name"></option>
                    </template>
                </select>
            </label>
            <label class="flex items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('common.status')"></span>
                <select x-model="statusFilter" @change="page = 1"
                    class="focus-ring rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <template x-for="s in statuses" :key="s">
                        <option :value="s" x-text="$t('status.' + s)"></option>
                    </template>
                </select>
            </label>
            <label class="flex items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('topicsAdmin.colAuthor')"></span>
                <select x-model="authorFilter" @change="page = 1"
                    class="focus-ring rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <template x-for="m in $store.forum.members" :key="m.id">
                        <option :value="String(m.id)" x-text="m.name"></option>
                    </template>
                </select>
            </label>
        </x-ui.filter-bar>

        <div class="surface-card overflow-hidden rounded-xl border">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed">
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('topicsAdmin.colTopic')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('topicsAdmin.colAuthor')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('topicsAdmin.colReplies')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('topicsAdmin.colScore')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('topicsAdmin.colLastReply')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('common.actions')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="paged.length === 0">
                            <tr><td colspan="6" class="px-5 py-10 text-center text-muted" x-text="$t('common.noResults')"></td></tr>
                        </template>
                        <template x-for="topic in paged" :key="topic.id">
                            <tr class="border-b border-themed last:border-0 hover:bg-gray-50 dark:hover:bg-white/5"
                                x-data="{ get author() { return $store.forum.memberById(topic.authorId) }, get category() { return $store.forum.categoryById(topic.categoryId) } }">
                                <td class="px-5 py-3 text-body">
                                    <div class="min-w-0 max-w-md">
                                        <a :href="'/app/topicos/' + topic.id"
                                            class="line-clamp-2 text-sm font-medium text-heading transition hover:text-brand-600" x-text="topic.title"></a>
                                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                            <x-fo.status-pill status="topic.status" />
                                            <span x-show="category" class="text-xs text-muted" x-text="category ? category.name : ''"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <template x-if="author">
                                        <div class="flex items-center gap-2">
                                            <x-ui.avatar src="author.avatar" name="author.name" size="xs" />
                                            <span class="truncate text-sm" x-text="author.name"></span>
                                        </div>
                                    </template>
                                    <template x-if="!author"><span class="text-xs text-muted">—</span></template>
                                </td>
                                <td class="px-5 py-3 text-body"><span class="tabular-nums" x-text="topic.replyCount"></span></td>
                                <td class="px-5 py-3 text-body"><span class="tabular-nums" x-text="foHelpers.score(topic)"></span></td>
                                <td class="px-5 py-3 text-body"><span class="text-xs text-muted" x-text="$rel(topic.lastReplyAt)"></span></td>
                                <td class="px-5 py-3 text-body">
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="{{ $iconBtn }}"
                                            :title="topic.status === 'fixado' ? $t('moderation.unpin') : $t('moderation.pin')"
                                            @click="$store.forum.togglePinned(topic.id); $store.ui.notifySuccess($t('moderation.done'))">
                                            <x-icon name="pin" :size="15" />
                                        </button>
                                        <button type="button" class="{{ $iconBtn }}"
                                            :title="topic.status === 'fechado' ? $t('moderation.reopen') : $t('moderation.close')"
                                            @click="$store.forum.toggleClosed(topic.id); $store.ui.notifySuccess($t('moderation.done'))">
                                            <template x-if="topic.status === 'fechado'"><x-icon name="unlock" :size="15" /></template>
                                            <template x-if="topic.status !== 'fechado'"><x-icon name="lock" :size="15" /></template>
                                        </button>
                                        <button type="button" class="{{ $iconBtn }}" :title="$t('moderation.move')"
                                            @click="moveTarget = topic; moveTo = String(topic.categoryId)">
                                            <x-icon name="move" :size="15" />
                                        </button>
                                        <button type="button" class="{{ $iconBtn }} hover:text-error-500" :title="$t('common.remove')"
                                            @click="removeTarget = topic">
                                            <x-icon name="trash" :size="15" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-themed p-4 text-sm text-muted">
                <span x-text="rangeLabel"></span>
                <div class="flex items-center gap-1">
                    <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page <= 1"
                        class="focus-ring rounded-lg border border-themed px-3 py-1.5 disabled:opacity-40" x-text="$t('common.previous')"></button>
                    <button type="button" @click="page = Math.min(totalPages, page + 1)" :disabled="page >= totalPages"
                        class="focus-ring rounded-lg border border-themed px-3 py-1.5 disabled:opacity-40" x-text="$t('common.next')"></button>
                </div>
            </div>
        </div>

        {{-- Mover de categoria --}}
        <x-ui.modal open="moveTarget !== null" close="moveTarget = null" title="$t('moderation.moveTitle')"
            subtitle="moveTarget ? moveTarget.title : ''">
            <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('search.category')"></label>
            <select x-model="moveTo" class="{{ $field }}">
                <template x-for="c in $store.forum.categories" :key="c.id">
                    <option :value="String(c.id)" x-text="c.name"></option>
                </template>
            </select>
            <x-slot:footer>
                <button type="button" @click="moveTarget = null" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doMove()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
                    x-text="$t('common.save')"></button>
            </x-slot:footer>
        </x-ui.modal>

        {{-- Excluir --}}
        <x-ui.modal open="removeTarget !== null" close="removeTarget = null" title="$t('moderation.removeTitle')"
            subtitle="removeTarget ? removeTarget.title : ''" size="sm">
            <p class="text-sm text-muted" x-text="$t('moderation.removeDesc')"></p>
            <x-slot:footer>
                <button type="button" @click="removeTarget = null" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doRemove()" class="rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white hover:bg-error-600"
                    x-text="$t('common.remove')"></button>
            </x-slot:footer>
        </x-ui.modal>

        {{-- Novo tópico --}}
        <x-ui.modal open="createOpen" close="createOpen = false" title="$t('topicsAdmin.newTitle')"
            subtitle="$t('topicsAdmin.newSubtitle')" size="lg">
            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('topicsAdmin.fieldTitle')"></label>
                    <input x-model="form.title" class="{{ $field }}" :placeholder="$t('topicsAdmin.fieldTitlePlaceholder')" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('search.category')"></label>
                        <select x-model="form.categoryId" class="{{ $field }}">
                            <option value="" x-text="$t('topicsAdmin.pickCategory')"></option>
                            <template x-for="c in $store.forum.categories" :key="c.id">
                                <option :value="String(c.id)" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('topicsAdmin.fieldTags')"></label>
                        <input x-model="form.tags" class="{{ $field }}" :placeholder="$t('topicsAdmin.fieldTagsPlaceholder')" />
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('topicsAdmin.fieldBody')"></label>
                    <textarea rows="6" x-model="form.body" class="{{ $field }} resize-y" :placeholder="$t('topicsAdmin.fieldBodyPlaceholder')"></textarea>
                </div>
            </div>
            <x-slot:footer>
                <button type="button" @click="createOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="createTopic()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
                    x-text="$t('common.save')"></button>
            </x-slot:footer>
        </x-ui.modal>
    </div>

    @push('scripts')
        <script>
            function topicsPage(openNew) {
                return {
                    search: '',
                    categoryFilter: 'all',
                    statusFilter: 'all',
                    authorFilter: 'all',
                    statuses: ['aberto', 'fixado', 'fechado', 'oculto'],
                    page: 1,
                    pageSize: 8,
                    moveTarget: null,
                    moveTo: '',
                    removeTarget: null,
                    createOpen: openNew,
                    form: { title: '', categoryId: '', tags: '', body: '' },
                    init() {
                        // ?novo=1 abre o modal e some da URL, igual ao React.
                        if (openNew) {
                            const params = new URLSearchParams(window.location.search);
                            params.delete('novo');
                            const qs = params.toString();
                            window.history.replaceState({}, '', qs ? '?' + qs : window.location.pathname);
                        }
                        this.$watch('search', () => { this.page = 1; });
                    },
                    get rows() {
                        return foHelpers
                            .filterTopics($store.forum.topics, {
                                search: this.search,
                                categoryId: this.categoryFilter === 'all' ? 'all' : Number(this.categoryFilter),
                                status: this.statusFilter === 'all' ? 'all' : this.statusFilter,
                                authorId: this.authorFilter === 'all' ? 'all' : Number(this.authorFilter),
                            })
                            .sort((a, b) => b.lastReplyAt.localeCompare(a.lastReplyAt));
                    },
                    get totalPages() { return foHelpers.pageCount(this.rows.length, this.pageSize); },
                    get paged() { return foHelpers.paginate(this.rows, Math.min(this.page, this.totalPages), this.pageSize); },
                    get rangeLabel() {
                        const total = this.rows.length;
                        if (total === 0) return '0 / 0';
                        const current = Math.min(this.page, this.totalPages);
                        const from = (current - 1) * this.pageSize + 1;
                        const to = Math.min(current * this.pageSize, total);
                        return from + '–' + to + ' / ' + total;
                    },
                    doMove() {
                        if (this.moveTarget) $store.forum.moveTopic(this.moveTarget.id, Number(this.moveTo));
                        this.moveTarget = null;
                        $store.ui.notifySuccess($t('moderation.moved'));
                    },
                    doRemove() {
                        if (this.removeTarget) $store.forum.removeTopic(this.removeTarget.id);
                        this.removeTarget = null;
                        $store.ui.notifySuccess($t('moderation.removed'));
                    },
                    createTopic() {
                        if (!this.form.title.trim() || !this.form.categoryId) return;
                        $store.forum.addTopic({
                            title: this.form.title.trim(),
                            categoryId: Number(this.form.categoryId),
                            authorId: $store.forum.currentMemberId,
                            body: this.form.body.trim() || $t('topicsAdmin.emptyBody'),
                            tags: this.form.tags.split(',').map(tag => tag.trim()).filter(Boolean),
                        });
                        this.form = { title: '', categoryId: '', tags: '', body: '' };
                        this.createOpen = false;
                        $store.ui.notifySuccess($t('topicsAdmin.created'));
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
