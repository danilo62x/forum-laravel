<x-layout.public :pageTitle="$pageTitle ?? null">
    <div x-data="memberPage(@js($slug))">
        <template x-if="!member">
            <div class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
                <x-ui.empty-state icon="user" titleKey="member.notFoundTitle" descKey="member.notFoundDesc">
                    <x-slot:action>
                        <a href="{{ route('home') }}" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white"
                            x-text="$t('notFound.home')"></a>
                    </x-slot:action>
                </x-ui.empty-state>
            </div>
        </template>

        <template x-if="member">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <x-ui.breadcrumb :items="[
                ['labelKey' => 'nav.home', 'href' => route('home'), 'icon' => 'grid'],
                ['labelKey' => 'member.breadcrumb'],
                ['labelExpr' => 'member ? member.name : \'\''],
            ]" />

            <div class="mt-5 grid gap-8 lg:grid-cols-3">
                <aside class="min-w-0 space-y-6">
                    <section class="surface-card rounded-2xl border p-6 text-center">
                        <div class="flex justify-center">
                            <x-ui.avatar src="member.avatar" name="member.name" size="xl" />
                        </div>
                        <h1 class="mt-4 text-xl font-bold text-heading" x-text="member.name"></h1>
                        <div class="mt-2 flex flex-wrap items-center justify-center gap-1.5">
                            <x-fo.role-pill role="member.role" />
                            <template x-if="member.banned"><x-fo.banned-pill /></template>
                            <template x-if="!member.banned && muted && member.mutedUntil"><x-fo.muted-pill until="member.mutedUntil" /></template>
                        </div>
                        <p class="mt-4 text-sm text-muted" x-text="member.bio"></p>
                        <p class="mt-4 flex items-center justify-center gap-1.5 text-xs text-muted">
                            <x-icon name="calendar" :size="13" />
                            <span x-text="$t('member.joined', { date: $date(member.joinedAt) })"></span>
                        </p>
                    </section>

                    <section class="surface-card rounded-2xl border p-5">
                        <h2 class="text-base font-semibold text-heading" x-text="$t('member.badgesTitle')"></h2>
                        <p x-show="member.badges.length === 0" class="mt-3 text-sm text-muted" x-text="$t('member.noBadges')"></p>
                        <div x-show="member.badges.length > 0" class="mt-3 flex flex-wrap gap-2">
                            <template x-for="badge in member.badges" :key="badge">
                                <x-fo.badge-pill name="badge" />
                            </template>
                        </div>
                        <p class="mt-3 text-xs text-muted" x-text="$t('member.badgesHint')"></p>
                    </section>
                </aside>

                <div class="min-w-0 lg:col-span-2">
                    <section class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <template x-for="s in stats" :key="s.key">
                            <div class="surface-card rounded-xl border p-4">
                                <template x-if="s.icon === 'star'"><x-icon name="star" :size="18" class="text-brand-500" /></template>
                                <template x-if="s.icon === 'message-square'"><x-icon name="message-square" :size="18" class="text-brand-500" /></template>
                                <template x-if="s.icon === 'message-circle'"><x-icon name="message-circle" :size="18" class="text-brand-500" /></template>
                                <template x-if="s.icon === 'check-circle'"><x-icon name="check-circle" :size="18" class="text-brand-500" /></template>
                                <p class="mt-2 text-lg font-bold text-heading" x-text="s.value"></p>
                                <p class="text-[11px] text-muted" x-text="$t(s.key)"></p>
                            </div>
                        </template>
                    </section>

                    <section class="surface-card mt-6 rounded-2xl border p-5">
                        <h2 class="text-base font-semibold text-heading" x-text="$t('member.activityTitle')"></h2>
                        <p class="mt-1 text-xs text-muted" x-text="$t('member.activityDesc')"></p>
                        <div class="mt-3 -mx-2 overflow-x-auto">
                            <div class="min-w-[320px] px-2"><div x-data="foChartBox(() => foChart.memberActivity(activity))"></div></div>
                        </div>
                    </section>

                    <section class="mt-6">
                        <x-ui.tabs :tabs="[
                            ['labelKey' => 'member.tabTopics', 'icon' => 'message-square'],
                            ['labelKey' => 'member.tabReplies', 'icon' => 'message-circle'],
                        ]" model="tab" />

                        <div class="pt-5 text-sm text-body">
                            {{-- Tópicos --}}
                            <div x-show="tab === 0">
                                <template x-if="ownTopics.length === 0">
                                    <x-ui.empty-state icon="message-square" titleKey="member.emptyTopics" />
                                </template>
                                <div x-show="ownTopics.length > 0" class="space-y-3">
                                    <template x-for="topic in ownTopics" :key="topic.id">
                                        <div><x-fo.topic-card topic="topic" /></div>
                                    </template>
                                </div>
                            </div>

                            {{-- Respostas --}}
                            <div x-show="tab === 1" x-cloak>
                                <template x-if="ownReplies.length === 0">
                                    <x-ui.empty-state icon="message-circle" titleKey="member.emptyReplies" />
                                </template>
                                <div x-show="ownReplies.length > 0" class="space-y-3">
                                    <template x-for="reply in ownReplies" :key="reply.id">
                                        <article class="surface-card rounded-xl border p-4"
                                            x-data="{ get parent() { return $store.forum.topicById(reply.topicId) } }">
                                            <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                                                <span x-show="reply.best"
                                                    class="inline-flex items-center gap-1 rounded-full bg-success-50 px-2 py-0.5 font-semibold text-success-700 dark:bg-success-500/15 dark:text-success-500">
                                                    <x-icon name="check-circle" :size="11" :strokeWidth="2.4" />
                                                    <span x-text="$t('topic.bestReply')"></span>
                                                </span>
                                                <span x-text="$rel(reply.at)"></span>
                                                <span x-text="'· ' + $t('member.replyScore', { value: foHelpers.score(reply) })"></span>
                                            </div>
                                            <template x-if="parent">
                                                <h3 class="mt-1.5 text-sm font-semibold text-heading">
                                                    <a :href="'/topico/' + parent.slug + '#resposta-' + reply.id"
                                                        class="transition hover:text-brand-600 dark:hover:text-brand-400" x-text="parent.title"></a>
                                                </h3>
                                            </template>
                                            <x-fo.rich-body text="reply.body" clamp="220" class="mt-1.5" />
                                        </article>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function memberPage(slug) {
                return {
                    slug,
                    tab: 0,
                    get member() { return $store.forum.memberBySlug(this.slug); },
                    get muted() { return foHelpers.isMuted(this.member); },
                    get ownTopics() { return this.member ? foHelpers.topicsOfAuthor($store.forum.topics, this.member.id) : []; },
                    get ownReplies() { return this.member ? foHelpers.repliesOfAuthor($store.forum.replies, this.member.id) : []; },
                    get activity() {
                        return this.member ? foHelpers.activityByMonth(this.member, $store.forum.topics, $store.forum.replies, 6) : [];
                    },
                    get stats() {
                        const m = this.member;
                        if (!m) return [];
                        return [
                            { key: 'member.statReputation', icon: 'star', value: $number(m.reputation) },
                            { key: 'member.statTopics', icon: 'message-square', value: m.topicCount },
                            { key: 'member.statReplies', icon: 'message-circle', value: m.replyCount },
                            { key: 'member.statBest', icon: 'check-circle', value: this.ownReplies.filter(r => r.best).length },
                        ];
                    },
                };
            }
        </script>
    @endpush
</x-layout.public>
