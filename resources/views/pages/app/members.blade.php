<x-layout.app>
    @php($iconBtn = 'flex h-8 w-8 items-center justify-center rounded-lg border border-themed text-muted transition hover:text-brand-500')

    <div x-data="membersPage()">
        <x-app.page-header title="membersAdmin.title" subtitle="membersAdmin.subtitle" :crumbs="[
            ['labelKey' => 'appNav.dashboard', 'href' => route('dashboard')],
            ['labelKey' => 'appNav.members'],
        ]" />

        <x-ui.filter-bar search="search" placeholderKey="membersAdmin.searchPlaceholder">
            <label class="flex items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('membersAdmin.colRole')"></span>
                <select x-model="roleFilter" @change="page = 1"
                    class="focus-ring rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <template x-for="role in roles" :key="role">
                        <option :value="role" x-text="$t('role.' + role)"></option>
                    </template>
                </select>
            </label>
            <label class="flex items-center gap-2 text-sm text-muted">
                <span class="whitespace-nowrap" x-text="$t('common.status')"></span>
                <select x-model="stateFilter" @change="page = 1"
                    class="focus-ring rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body">
                    <option value="all" x-text="$t('common.all')"></option>
                    <option value="ativos" x-text="$t('membersAdmin.active')"></option>
                    <option value="silenciados" x-text="$t('membersAdmin.mutedFilter')"></option>
                    <option value="banidos" x-text="$t('members.banned')"></option>
                </select>
            </label>
        </x-ui.filter-bar>

        <div class="surface-card overflow-hidden rounded-xl border">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed">
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('membersAdmin.colMember')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('membersAdmin.colRole')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('membersAdmin.colReputation')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('membersAdmin.colActivity')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('membersAdmin.colBadges')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('common.status')"></th>
                            <th class="px-5 py-3 font-medium text-muted" x-text="$t('common.actions')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="paged.length === 0">
                            <tr><td colspan="7" class="px-5 py-10 text-center text-muted" x-text="$t('common.noResults')"></td></tr>
                        </template>
                        <template x-for="member in paged" :key="member.id">
                            <tr class="border-b border-themed last:border-0 hover:bg-gray-50 dark:hover:bg-white/5"
                                x-data="{ get muted() { return foHelpers.isMuted(member) } }">
                                <td class="px-5 py-3 text-body">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <x-ui.avatar src="member.avatar" name="member.name" size="sm" />
                                        <div class="min-w-0">
                                            <a :href="'/membro/' + member.slug"
                                                class="block truncate text-sm font-medium text-heading transition hover:text-brand-600" x-text="member.name"></a>
                                            <p class="truncate text-xs text-muted" x-text="$t('membersAdmin.joined', { date: $date(member.joinedAt) })"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <select :value="member.role"
                                        @change="$store.forum.setMemberRole(member.id, $event.target.value); $store.ui.notifySuccess($t('membersAdmin.roleChanged'))"
                                        class="focus-ring rounded-lg border border-themed bg-transparent px-2 py-1.5 text-xs text-body">
                                        <template x-for="role in roles" :key="role">
                                            <option :value="role" x-text="$t('role.' + role)"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <span class="font-semibold tabular-nums text-heading" x-text="$number(member.reputation)"></span>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <span class="text-xs text-muted"
                                        x-text="$t('membersAdmin.activity', { topics: member.topicCount, replies: member.replyCount })"></span>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <div class="flex max-w-[220px] flex-wrap gap-1">
                                        <template x-for="badge in member.badges.slice(0, 2)" :key="badge">
                                            <x-fo.badge-pill name="badge" size="sm" />
                                        </template>
                                        <span x-show="member.badges.length > 2" class="text-[11px] text-muted"
                                            x-text="'+' + (member.badges.length - 2)"></span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <template x-if="member.banned"><x-fo.banned-pill /></template>
                                    <template x-if="!member.banned && muted && member.mutedUntil"><x-fo.muted-pill until="member.mutedUntil" /></template>
                                    <template x-if="!member.banned && !muted">
                                        <span class="text-xs text-success-600 dark:text-success-500" x-text="$t('membersAdmin.active')"></span>
                                    </template>
                                </td>
                                <td class="px-5 py-3 text-body">
                                    <div class="flex items-center gap-1">
                                        <button type="button" x-show="muted" class="{{ $iconBtn }}" :title="$t('membersAdmin.unmute')"
                                            @click="$store.forum.unmuteMember(member.id); $store.ui.notifySuccess($t('membersAdmin.unmuted'))">
                                            <x-icon name="volume-x" :size="15" />
                                        </button>
                                        <button type="button" x-show="!muted" class="{{ $iconBtn }}" :title="$t('membersAdmin.mute')"
                                            @click="muteTarget = member; muteDays = 7">
                                            <x-icon name="volume-x" :size="15" />
                                        </button>
                                        <button type="button" class="{{ $iconBtn }} hover:text-error-500"
                                            :class="member.banned ? 'text-error-500' : ''"
                                            :title="member.banned ? $t('membersAdmin.unban') : $t('membersAdmin.ban')"
                                            @click="member.banned
                                                ? ($store.forum.setMemberBanned(member.id, false), $store.ui.notifySuccess($t('membersAdmin.unbanned')))
                                                : (banTarget = member)">
                                            <x-icon name="user-x" :size="15" />
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

        {{-- Silenciar --}}
        <x-ui.modal open="muteTarget !== null" close="muteTarget = null" title="$t('membersAdmin.muteTitle')"
            subtitle="muteTarget ? muteTarget.name : ''">
            <p class="text-sm text-muted" x-text="$t('membersAdmin.muteDesc')"></p>
            <div class="mt-4 flex flex-wrap gap-2">
                <template x-for="days in muteOptions" :key="days">
                    <button type="button" @click="muteDays = days"
                        class="rounded-lg border px-4 py-2 text-sm font-medium transition"
                        :class="muteDays === days ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-themed text-body'"
                        x-text="$t('duration.days', { count: days })"></button>
                </template>
            </div>
            <x-slot:footer>
                <button type="button" @click="muteTarget = null" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doMute()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
                    x-text="$t('common.confirm')"></button>
            </x-slot:footer>
        </x-ui.modal>

        {{-- Banir --}}
        <x-ui.modal open="banTarget !== null" close="banTarget = null" title="$t('membersAdmin.banTitle')"
            subtitle="banTarget ? banTarget.name : ''" size="sm">
            <p class="text-sm text-muted" x-text="$t('membersAdmin.banDesc')"></p>
            <x-slot:footer>
                <button type="button" @click="banTarget = null" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body"
                    x-text="$t('common.cancel')"></button>
                <button type="button" @click="doBan()" class="rounded-lg bg-error-500 px-4 py-2 text-sm font-medium text-white hover:bg-error-600"
                    x-text="$t('membersAdmin.ban')"></button>
            </x-slot:footer>
        </x-ui.modal>
    </div>

    @push('scripts')
        <script>
            function membersPage() {
                return {
                    search: '',
                    roleFilter: 'all',
                    stateFilter: 'all',
                    roles: ['membro', 'moderador', 'admin'],
                    muteOptions: [1, 3, 7, 30],
                    muteTarget: null,
                    muteDays: 7,
                    banTarget: null,
                    page: 1,
                    pageSize: 10,
                    init() {
                        this.$watch('search', () => { this.page = 1; });
                    },
                    get rows() {
                        const term = this.search.trim().toLowerCase();
                        return $store.forum.members
                            .filter(m => {
                                if (this.roleFilter !== 'all' && m.role !== this.roleFilter) return false;
                                if (this.stateFilter === 'ativos' && (m.banned || foHelpers.isMuted(m))) return false;
                                if (this.stateFilter === 'silenciados' && !foHelpers.isMuted(m)) return false;
                                if (this.stateFilter === 'banidos' && !m.banned) return false;
                                if (!term) return true;
                                return (m.name + ' ' + m.slug + ' ' + m.bio).toLowerCase().includes(term);
                            })
                            .sort((a, b) => b.reputation - a.reputation);
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
                    doMute() {
                        if (this.muteTarget) $store.forum.muteMember(this.muteTarget.id, this.muteDays);
                        this.muteTarget = null;
                        $store.ui.notifySuccess($t('membersAdmin.muted'));
                    },
                    doBan() {
                        if (this.banTarget) $store.forum.setMemberBanned(this.banTarget.id, true);
                        this.banTarget = null;
                        $store.ui.notifySuccess($t('membersAdmin.banned'));
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
