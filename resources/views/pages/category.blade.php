<x-layout.public>
    @if (! $category)
        <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
            <x-ui.empty-state icon="folder" titleKey="category.notFoundTitle" descKey="category.notFoundDesc">
                <x-slot:action>
                    <a href="{{ route('home') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white"
                        x-data x-text="$t('notFound.home')"></a>
                </x-slot:action>
            </x-ui.empty-state>
        </div>
    @else
        <div x-data="categoryPage(@js($slug))">
            <header class="relative overflow-hidden border-b border-themed">
                <img src="{{ $cover }}" alt="" aria-hidden class="absolute inset-0 h-full w-full object-cover opacity-15" />
                <div class="relative mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <x-ui.breadcrumb :items="[
                        ['labelKey' => 'nav.home', 'href' => route('home'), 'icon' => 'grid'],
                        ['labelExpr' => 'category ? category.name : \'\''],
                    ]" />
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-start">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl"
                            :style="{ backgroundColor: category.color + '1a', color: category.color }">
                            <x-fo.category-icon icon="category.icon" :size="26" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-2xl font-bold text-heading" x-text="category.name"></h1>
                            <p class="mt-2 max-w-3xl text-sm text-muted" x-text="category.description"></p>
                            <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-muted">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="message-square" :size="13" />
                                    <span x-text="$t('category.topicCount', { count: category.topicCount })"></span>
                                </span>
                                <span x-show="category.staffOnly" class="flex items-center gap-1.5">
                                    <x-icon name="lock" :size="13" />
                                    <span x-text="$t('category.staffOnly')"></span>
                                </span>
                                <span class="flex flex-wrap items-center gap-2">
                                    <span x-text="$t('category.moderatedBy') + ':'"></span>
                                    <template x-for="m in moderators" :key="m.id">
                                        <a :href="'/membro/' + m.slug"
                                            class="flex items-center gap-1.5 rounded-full border border-themed px-2 py-1 transition hover:text-brand-600">
                                            <x-ui.avatar src="m.avatar" name="m.name" size="xs" />
                                            <span x-text="m.name"></span>
                                        </a>
                                    </template>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="surface-card mb-5 flex flex-col gap-3 rounded-xl border p-3 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-wrap gap-1">
                        <template x-for="option in sorts" :key="option.value">
                            <button type="button" @click="sort = option.value; page = 1"
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium transition"
                                :class="sort === option.value ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' : 'text-muted hover:text-heading'">
                                <template x-if="option.icon === 'clock'"><x-icon name="clock" :size="15" /></template>
                                <template x-if="option.icon === 'trending-up'"><x-icon name="trending-up" :size="15" /></template>
                                <template x-if="option.icon === 'help'"><x-icon name="help" :size="15" /></template>
                                <span x-text="$t(option.labelKey)"></span>
                            </button>
                        </template>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-muted">
                        <x-icon name="hash" :size="15" />
                        <select x-model="tag" @change="page = 1"
                            class="focus-ring min-w-0 rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                            <option value="all" x-text="$t('category.allTags')"></option>
                            <template x-for="item in tags" :key="item">
                                <option :value="item" x-text="item"></option>
                            </template>
                        </select>
                    </label>
                </div>

                <template x-if="paged.length === 0">
                    <x-ui.empty-state icon="message-square" titleKey="category.emptyTitle" descKey="category.emptyDesc">
                        <x-slot:action>
                            <button type="button" @click="tag = 'all'; sort = 'recentes'; page = 1"
                                class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body" x-text="$t('common.clearFilters')"></button>
                        </x-slot:action>
                    </x-ui.empty-state>
                </template>

                <div x-show="paged.length > 0">
                    <div class="space-y-3">
                        <template x-for="topic in paged" :key="topic.id">
                            <div><x-fo.topic-card topic="topic" :showCategory="false" /></div>
                        </template>
                    </div>
                    <div class="mt-8">
                        <x-ui.pagination page="page" totalPages="totalPages" />
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            function categoryPage(slug) {
                return {
                    slug,
                    sort: 'recentes',
                    tag: 'all',
                    page: 1,
                    pageSize: 8,
                    sorts: [
                        { value: 'recentes', labelKey: 'category.sortRecent', icon: 'clock' },
                        { value: 'votados', labelKey: 'category.sortVoted', icon: 'trending-up' },
                        { value: 'sem-resposta', labelKey: 'category.sortUnanswered', icon: 'help' },
                    ],
                    get category() { return $store.forum.categoryBySlug(this.slug) || {}; },
                    get moderators() {
                        return (this.category.moderatorIds || []).map(id => $store.forum.memberById(id)).filter(Boolean);
                    },
                    get scoped() {
                        return this.category.id ? foHelpers.topicsOfCategory($store.forum.topics, this.category.id) : [];
                    },
                    get tags() { return foHelpers.allTags(this.scoped); },
                    get rows() {
                        const filtered = this.tag === 'all' ? this.scoped : this.scoped.filter(t => t.tags.includes(this.tag));
                        return foHelpers.sortTopics(filtered, this.sort);
                    },
                    get totalPages() { return foHelpers.pageCount(this.rows.length, this.pageSize); },
                    get paged() { return foHelpers.paginate(this.rows, Math.min(this.page, this.totalPages), this.pageSize); },
                };
            }
        </script>
    @endpush
</x-layout.public>
