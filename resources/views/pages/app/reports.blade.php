<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="reportsPage()">
        <x-app.page-header title="reports.title" subtitle="reports.subtitle" :crumbs="[
            ['labelKey' => 'appNav.dashboard', 'href' => route('dashboard')],
            ['labelKey' => 'appNav.reports'],
        ]" />

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <template x-for="counter in counters" :key="counter.reason">
                <button type="button" @click="reason = reason === counter.reason ? 'all' : counter.reason"
                    class="surface-card rounded-2xl border p-5 text-left transition"
                    :class="reason === counter.reason ? 'border-brand-400 ring-1 ring-brand-300' : 'hover:border-brand-300'">
                    <p class="text-xs text-muted" x-text="$t('reason.' + counter.reason)"></p>
                    <p class="mt-2 text-2xl font-bold text-heading" x-text="counter.value"></p>
                </button>
            </template>
        </div>

        <div class="surface-card mt-6 flex flex-col gap-3 rounded-xl border p-4 sm:flex-row sm:items-center">
            <div class="flex flex-wrap gap-1">
                <button type="button" @click="showHistory = false"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition"
                    :class="!showHistory ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' : 'text-muted'"
                    x-text="$t('reports.tabQueue', { count: openCount })"></button>
                <button type="button" @click="showHistory = true"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition"
                    :class="showHistory ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' : 'text-muted'"
                    x-text="$t('reports.tabHistory')"></button>
            </div>

            <label class="flex min-w-0 items-center gap-2 text-sm text-muted sm:ml-auto">
                <x-icon name="filter" :size="15" />
                <select x-model="reason" class="focus-ring min-w-0 rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('reports.allReasons')"></option>
                    <template x-for="item in reasons" :key="item">
                        <option :value="item" x-text="$t('reason.' + item)"></option>
                    </template>
                </select>
            </label>
        </div>

        <div class="mt-5 space-y-3">
            <template x-if="rows.length === 0">
                <div>
                    <div x-show="showHistory">
                        <x-ui.empty-state icon="shield-check" titleKey="reports.emptyHistoryTitle" descKey="reports.emptyHistoryDesc" />
                    </div>
                    <div x-show="!showHistory">
                        <x-ui.empty-state icon="shield-check" titleKey="reports.emptyQueueTitle" descKey="reports.emptyQueueDesc" />
                    </div>
                </div>
            </template>
            <template x-for="row in rows" :key="row.report.id">
                <div><x-fo.report-row row="row" /></div>
            </template>
        </div>
    </div>

    @push('scripts')
        <script>
            function reportsPage() {
                return {
                    reason: 'all',
                    showHistory: false,
                    reasons: ['spam', 'ofensa', 'fora-do-tema', 'duplicado'],
                    get counters() { return foHelpers.reportsByReason($store.forum.reports); },
                    get openCount() { return $store.forum.reports.filter(r => !r.resolved).length; },
                    get rows() {
                        const scoped = $store.forum.reports.filter(r => r.resolved === this.showHistory);
                        const filtered = this.reason === 'all' ? scoped : scoped.filter(r => r.reason === this.reason);
                        return foHelpers.reportRows(filtered, $store.forum.topics, $store.forum.replies, $store.forum.members);
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
