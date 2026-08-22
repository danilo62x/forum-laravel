<x-layout.public>
    <div x-data="homePage()">
        {{-- Hero com a busca em destaque --}}
        <section class="relative overflow-hidden border-b border-themed bg-gradient-to-br from-brand-50 via-white to-brand-100 dark:from-gray-900 dark:via-gray-dark dark:to-gray-900">
            <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-500/10"></div>
            <div class="relative mx-auto max-w-4xl px-4 py-14 text-center sm:px-6 md:py-20 lg:px-8">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-medium text-brand-600 shadow-theme-xs dark:bg-white/10 dark:text-brand-400">
                    <x-icon name="sparkles" :size="13" />
                    <span data-i18n="home.badge" x-text="$t('home.badge')"></span>
                </span>
                <h1 class="mt-5 text-3xl font-bold text-heading sm:text-title-md" data-i18n="home.title" x-text="$t('home.title')"></h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm text-muted sm:text-base" data-i18n="home.subtitle" x-text="$t('home.subtitle')"></p>

                <form method="GET" action="{{ route('search') }}" class="mx-auto mt-8 flex w-full max-w-2xl flex-col gap-2 sm:flex-row">
                    <div class="relative min-w-0 flex-1">
                        <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-muted">
                            <x-icon name="search" :size="18" />
                        </span>
                        <input name="q" x-model="term" data-i18n-ph="home.searchPlaceholder" :placeholder="$t('home.searchPlaceholder')"
                            class="focus-ring surface-card h-12 w-full rounded-xl border pr-4 pl-11 text-sm text-body shadow-theme-xs placeholder:text-muted" />
                    </div>
                    <button type="submit"
                        class="flex h-12 shrink-0 items-center justify-center rounded-xl bg-brand-500 px-6 text-sm font-medium text-white transition hover:bg-brand-600"
                        x-text="$t('common.search')"></button>
                </form>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-8 gap-y-3">
                    <template x-for="s in stats" :key="s.key">
                        <div class="flex items-center gap-2 text-sm text-muted">
                            <template x-if="s.icon === 'message-square'"><x-icon name="message-square" :size="16" class="text-brand-500" /></template>
                            <template x-if="s.icon === 'message-circle'"><x-icon name="message-circle" :size="16" class="text-brand-500" /></template>
                            <template x-if="s.icon === 'users'"><x-icon name="users" :size="16" class="text-brand-500" /></template>
                            <span class="font-semibold text-heading" x-text="$number(s.value)"></span>
                            <span x-text="$t(s.key)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            {{-- Categorias --}}
            <section>
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-xl font-bold text-heading" data-i18n="home.categoriesTitle" x-text="$t('home.categoriesTitle')"></h2>
                        <p class="mt-1 text-sm text-muted" data-i18n="home.categoriesDesc" x-text="$t('home.categoriesDesc')"></p>
                    </div>
                </div>
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <template x-for="category in $store.forum.categories" :key="category.id">
                        <x-fo.category-card category="category" replyCount="replyCountOf(category.id)" />
                    </template>
                </div>
            </section>

            <div class="mt-12 grid gap-8 lg:grid-cols-3">
                <div class="min-w-0 lg:col-span-2">
                    {{-- Em alta --}}
                    <section>
                        <div class="flex items-center gap-2">
                            <x-icon name="flame" :size="18" class="text-error-500" />
                            <h2 class="text-xl font-bold text-heading" data-i18n="home.trendingTitle" x-text="$t('home.trendingTitle')"></h2>
                        </div>
                        <p class="mt-1 text-sm text-muted" data-i18n="home.trendingDesc" x-text="$t('home.trendingDesc')"></p>
                        <div class="mt-5 space-y-3">
                            <template x-if="trending.length === 0">
                                <x-ui.empty-state icon="flame" titleKey="home.emptyTrending" />
                            </template>
                            <template x-for="topic in trending" :key="topic.id">
                                <div><x-fo.topic-card topic="topic" /></div>
                            </template>
                        </div>
                    </section>

                    {{-- Últimos tópicos --}}
                    <section class="mt-10">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <h2 class="text-xl font-bold text-heading" data-i18n="home.latestTitle" x-text="$t('home.latestTitle')"></h2>
                            <a href="{{ route('search') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600"
                                x-text="$t('common.viewAll') + ' →'"></a>
                        </div>
                        <div class="mt-5 space-y-3">
                            <template x-for="topic in latest" :key="topic.id">
                                <div><x-fo.topic-card topic="topic" /></div>
                            </template>
                        </div>
                    </section>
                </div>

                <aside class="min-w-0 space-y-8">
                    <section class="surface-card rounded-2xl border p-5">
                        <h2 class="text-base font-semibold text-heading" data-i18n="home.unansweredTitle" x-text="$t('home.unansweredTitle')"></h2>
                        <p class="mt-1 text-xs text-muted" data-i18n="home.unansweredDesc" x-text="$t('home.unansweredDesc')"></p>
                        <div class="mt-4 space-y-3">
                            <template x-if="unanswered.length === 0">
                                <p class="text-sm text-muted" x-text="$t('home.emptyUnanswered')"></p>
                            </template>
                            <template x-for="topic in unanswered" :key="topic.id">
                                <a :href="'/topico/' + topic.slug" class="block rounded-lg px-2 py-2 transition hover:bg-gray-50 dark:hover:bg-white/5">
                                    <p class="line-clamp-2 text-sm font-medium text-heading" x-text="topic.title"></p>
                                    <p class="mt-1 text-xs text-muted" x-text="topic.tags.map(tag => '#' + tag).join(' ')"></p>
                                </a>
                            </template>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-base font-semibold text-heading" data-i18n="home.membersTitle" x-text="$t('home.membersTitle')"></h2>
                        <p class="mt-1 text-xs text-muted" data-i18n="home.membersDesc" x-text="$t('home.membersDesc')"></p>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <template x-for="member in featured" :key="member.id">
                                <div><x-fo.member-card member="member" /></div>
                            </template>
                        </div>
                    </section>

                    <section class="rounded-2xl bg-brand-500 p-6 text-white">
                        <x-icon name="megaphone" :size="24" />
                        <h2 class="mt-3 text-lg font-semibold" data-i18n="home.ctaTitle" x-text="$t('home.ctaTitle')"></h2>
                        <p class="mt-1 text-sm text-white/80" data-i18n="home.ctaDesc" x-text="$t('home.ctaDesc')"></p>
                        <a href="{{ route('rules') }}"
                            class="mt-4 inline-flex h-10 items-center rounded-lg bg-white px-4 text-sm font-medium text-brand-600 transition hover:bg-brand-50"
                            x-text="$t('home.ctaButton')"></a>
                    </section>
                </aside>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function homePage() {
                return {
                    term: '',
                    get open() { return foHelpers.visibleTopics($store.forum.topics); },
                    get trending() { return foHelpers.trendingTopics($store.forum.topics, 5); },
                    get latest() { return foHelpers.latestTopics($store.forum.topics, 6); },
                    get unanswered() { return foHelpers.unansweredTopics($store.forum.topics, 4); },
                    get featured() { return foHelpers.topMembers($store.forum.members, 4); },
                    get stats() {
                        return [
                            { key: 'home.statTopics', icon: 'message-square', value: this.open.length },
                            { key: 'home.statReplies', icon: 'message-circle', value: $store.forum.replies.filter(r => !r.hidden).length },
                            { key: 'home.statMembers', icon: 'users', value: $store.forum.members.filter(m => !m.banned).length },
                        ];
                    },
                    replyCountOf(categoryId) {
                        return this.open
                            .filter(topic => topic.categoryId === categoryId)
                            .reduce((sum, topic) => sum + foHelpers.repliesOfTopic($store.forum.replies, topic.id).length, 0);
                    },
                };
            }
        </script>
    @endpush
</x-layout.public>
