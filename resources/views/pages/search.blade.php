<x-layout.public>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8"
        x-data="searchPage(@js($query), @js($categoryParam), @js($tagParam), @js($unansweredParam))">
        <h1 class="text-2xl font-bold text-heading" data-i18n="search.title" x-text="$t('search.title')"></h1>
        <p class="mt-1 text-sm text-muted" data-i18n="search.subtitle" x-text="$t('search.subtitle')"></p>

        <form class="mt-6 flex flex-col gap-2 sm:flex-row" @submit.prevent="apply('q', term.trim())">
            <div class="relative min-w-0 flex-1">
                <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-muted">
                    <x-icon name="search" :size="18" />
                </span>
                <input x-model="term" data-i18n-ph="search.placeholder" :placeholder="$t('search.placeholder')"
                    class="focus-ring surface-card h-12 w-full rounded-xl border pr-4 pl-11 text-sm text-body placeholder:text-muted" />
            </div>
            <button type="submit"
                class="flex h-12 shrink-0 items-center justify-center rounded-xl bg-brand-500 px-6 text-sm font-medium text-white transition hover:bg-brand-600"
                x-text="$t('common.search')"></button>
        </form>

        <div class="surface-card mt-4 flex flex-col gap-3 rounded-xl border p-4 lg:flex-row lg:items-center">
            <label class="flex min-w-0 items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('search.category')"></span>
                <select x-model="categoryId" @change="apply('categoria', categoryId)"
                    class="focus-ring min-w-0 rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <template x-for="c in $store.forum.categories" :key="c.id">
                        <option :value="String(c.id)" x-text="c.name"></option>
                    </template>
                </select>
            </label>

            <label class="flex min-w-0 items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('search.tag')"></span>
                <select x-model="tag" @change="apply('tag', tag)"
                    class="focus-ring min-w-0 rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <template x-for="item in tags" :key="item">
                        <option :value="item" x-text="item"></option>
                    </template>
                </select>
            </label>

            <label class="flex items-center gap-2 text-sm text-muted">
                <x-ui.switch-toggle checked="unansweredOnly" toggle="unansweredOnly = !unansweredOnly; apply('sem-resposta', unansweredOnly ? '1' : null)" size="sm" />
                <span x-text="$t('search.onlyUnanswered')"></span>
            </label>

            <button type="button" x-show="hasFilters" x-cloak @click="clearFilters()"
                class="text-sm font-medium text-brand-500 hover:text-brand-600 lg:ml-auto" x-text="$t('common.clearFilters')"></button>
        </div>

        <p class="mt-5 text-sm text-muted" x-text="$t('search.results', { count: hits.length })"></p>

        <div class="mt-4 space-y-3">
            <template x-if="hits.length === 0">
                <x-ui.empty-state icon="search" titleKey="search.emptyTitle" descKey="search.emptyDesc" />
            </template>
            <template x-for="hit in hits" :key="hit.topic.id + '-' + (hit.reply ? hit.reply.id : 0)">
                <div><x-fo.search-hit-row hit="hit" /></div>
            </template>
        </div>
    </div>

    @push('scripts')
        <script>
            function searchPage(query, categoryParam, tagParam, unansweredParam) {
                return {
                    term: query,
                    query,
                    categoryId: categoryParam,
                    tag: tagParam,
                    unansweredOnly: unansweredParam,
                    get tags() { return foHelpers.allTags(foHelpers.visibleTopics($store.forum.topics)); },
                    get hits() {
                        return foHelpers.searchForum($store.forum.topics, $store.forum.replies, this.query, {
                            categoryId: this.categoryId === 'all' ? 'all' : Number(this.categoryId),
                            tag: this.tag,
                            onlyUnanswered: this.unansweredOnly,
                        });
                    },
                    get hasFilters() {
                        return this.categoryId !== 'all' || this.tag !== 'all' || this.unansweredOnly;
                    },
                    /** Mantém a query string em dia — a URL é o estado, igual ao React. */
                    apply(key, value) {
                        const params = new URLSearchParams(window.location.search);
                        if (!value || value === 'all' || value === '') params.delete(key);
                        else params.set(key, value);
                        if (key === 'q') this.query = value || '';
                        const qs = params.toString();
                        window.history.replaceState({}, '', qs ? '?' + qs : window.location.pathname);
                    },
                    clearFilters() {
                        this.categoryId = 'all';
                        this.tag = 'all';
                        this.unansweredOnly = false;
                        const params = new URLSearchParams();
                        if (this.query) params.set('q', this.query);
                        const qs = params.toString();
                        window.history.replaceState({}, '', qs ? '?' + qs : window.location.pathname);
                    },
                };
            }
        </script>
    @endpush
</x-layout.public>
