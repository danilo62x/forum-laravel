<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="dashboardPage()">
        <x-app.page-header title="dashboard.title" subtitle="dashboard.subtitle">
            <x-slot:action>
                <a href="{{ route('app.reports') }}"
                    class="flex h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                    <x-icon name="flag" :size="16" />
                    <span x-text="$t('dashboard.openQueue')"></span>
                </a>
            </x-slot:action>
        </x-app.page-header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stats-card titleKey="dashboard.kpiOpen" value="open.length" icon="message-square" accent="brand" />
            <x-ui.stats-card titleKey="dashboard.kpiReplies" value="today.length" icon="message-circle" accent="success" />
            <x-ui.stats-card titleKey="dashboard.kpiReports" value="queue.length" icon="flag" accent="error" />
            <x-ui.stats-card titleKey="dashboard.kpiMembers" value="fresh.length" icon="users" accent="info" />
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section class="surface-card min-w-0 rounded-2xl border p-5 xl:col-span-2">
                <h2 class="text-base font-semibold text-heading" data-i18n="dashboard.chartTitle" x-text="$t('dashboard.chartTitle')"></h2>
                <p class="mt-1 text-xs text-muted" data-i18n="dashboard.chartDesc" x-text="$t('dashboard.chartDesc')"></p>
                <div class="mt-4 -mx-2 overflow-x-auto">
                    <div class="min-w-[420px] px-2"><div x-data="foChartBox(() => foChart.activity(series))"></div></div>
                </div>
            </section>

            <section class="surface-card min-w-0 rounded-2xl border p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="flame" :size="16" class="text-error-500" />
                    <h2 class="text-base font-semibold text-heading" data-i18n="dashboard.trendingTitle" x-text="$t('dashboard.trendingTitle')"></h2>
                </div>
                <ul class="mt-4 space-y-3">
                    <template x-for="topic in trending" :key="topic.id">
                        <li>
                            <a :href="'/app/topicos/' + topic.id" class="block rounded-lg px-2 py-2 transition hover:bg-gray-50 dark:hover:bg-white/5">
                                <p class="line-clamp-2 text-sm font-medium text-heading" x-text="topic.title"></p>
                                <p class="mt-1 text-xs text-muted"
                                    x-text="($store.forum.categoryById(topic.categoryId)?.name || '') + ' · ' + $t('topic.score') + ' ' + foHelpers.score(topic) + ' · ' + $t('topic.replies', { count: topic.replyCount })"></p>
                            </a>
                        </li>
                    </template>
                </ul>
            </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="surface-card min-w-0 rounded-2xl border p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-semibold text-heading" data-i18n="dashboard.unansweredTitle" x-text="$t('dashboard.unansweredTitle')"></h2>
                    <a href="{{ route('app.topics') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600" x-text="$t('common.viewAll')"></a>
                </div>
                <template x-if="unanswered.length === 0">
                    <x-ui.empty-state icon="check-circle" titleKey="dashboard.noUnanswered" />
                </template>
                <ul x-show="unanswered.length > 0" class="mt-4 divide-y divide-themed">
                    <template x-for="topic in unanswered" :key="topic.id">
                        <li class="py-3 first:pt-0 last:pb-0" x-data="{ get author() { return $store.forum.memberById(topic.authorId) } }">
                            <a :href="'/app/topicos/' + topic.id" class="flex items-start gap-3">
                                <template x-if="author"><x-ui.avatar src="author.avatar" name="author.name" size="xs" /></template>
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-medium text-heading" x-text="topic.title"></p>
                                    <p class="mt-0.5 text-xs text-muted" x-text="(author ? author.name : '') + ' · ' + $rel(topic.at)"></p>
                                </div>
                                <x-icon name="chevron-right" :size="16" class="mt-1 shrink-0 text-gray-400" />
                            </a>
                        </li>
                    </template>
                </ul>
            </section>

            <section class="surface-card min-w-0 rounded-2xl border p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-base font-semibold text-heading" data-i18n="dashboard.reportsTitle" x-text="$t('dashboard.reportsTitle')"></h2>
                    <a href="{{ route('app.reports') }}" class="text-sm font-medium text-brand-500 hover:text-brand-600" x-text="$t('common.viewAll')"></a>
                </div>
                <template x-if="recentReports.length === 0">
                    <x-ui.empty-state icon="shield-check" titleKey="dashboard.noReports" />
                </template>
                <ul x-show="recentReports.length > 0" class="mt-4 divide-y divide-themed">
                    <template x-for="row in recentReports" :key="row.report.id">
                        <li class="py-3 first:pt-0 last:pb-0">
                            <a :href="row.topic ? (row.reply ? '/app/topicos/' + row.topic.id + '#resposta-' + row.reply.id : '/app/topicos/' + row.topic.id) : '/app/denuncias'"
                                class="flex items-start gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-fo.reason-pill reason="row.report.reason" />
                                        <span class="text-xs text-muted" x-text="$rel(row.report.at)"></span>
                                    </div>
                                    <p class="mt-1 line-clamp-1 text-sm font-medium text-heading"
                                        x-text="row.topic ? row.topic.title : $t('reports.targetGone')"></p>
                                    <p class="mt-0.5 line-clamp-1 text-xs text-muted"
                                        x-text="$t('reports.reportedBy', { name: row.reporter ? row.reporter.name : '—' })"></p>
                                </div>
                                <x-icon name="chevron-right" :size="16" class="mt-1 shrink-0 text-gray-400" />
                            </a>
                        </li>
                    </template>
                </ul>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            function dashboardPage() {
                return {
                    get open() {
                        return foHelpers.visibleTopics($store.forum.topics).filter(t => t.status !== 'fechado');
                    },
                    get today() { return foHelpers.repliesToday($store.forum.replies.filter(r => !r.hidden)); },
                    get queue() { return foHelpers.openReports($store.forum.reports); },
                    get fresh() { return foHelpers.newMembers($store.forum.members, 30); },
                    get series() { return foHelpers.activityByDay($store.forum.topics, $store.forum.replies, 14); },
                    get unanswered() { return foHelpers.unansweredTopics($store.forum.topics, 6); },
                    get trending() { return foHelpers.trendingTopics($store.forum.topics, 5); },
                    get recentReports() {
                        return foHelpers
                            .reportRows(this.queue, $store.forum.topics, $store.forum.replies, $store.forum.members)
                            .slice(0, 5);
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
