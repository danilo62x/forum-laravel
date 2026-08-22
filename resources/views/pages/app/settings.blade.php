<x-layout.app>
    @php($field = 'focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body placeholder:text-muted')
    @php($row = 'flex items-center justify-between gap-4 border-b border-themed py-4 last:border-0')

    <div x-data="settingsPage()">
        <x-app.page-header title="settings.title" subtitle="settings.subtitle" :crumbs="[
            ['labelKey' => 'appNav.dashboard', 'href' => route('dashboard')],
            ['labelKey' => 'appNav.settings'],
        ]" />

        <x-ui.tabs :tabs="[
            ['labelKey' => 'settings.tabProfile', 'icon' => 'user'],
            ['labelKey' => 'settings.tabCommunity', 'icon' => 'users'],
            ['labelKey' => 'settings.tabModeration', 'icon' => 'shield-check'],
            ['labelKey' => 'settings.tabNotifications', 'icon' => 'bell'],
            ['labelKey' => 'settings.tabAppearance', 'icon' => 'palette'],
        ]" model="tab" />

        <div class="pt-5 text-sm text-body">
            {{-- Perfil --}}
            <div x-show="tab === 0" class="surface-card rounded-2xl border p-5">
                <div class="flex items-center gap-4">
                    <x-ui.avatar name="profile.name" size="lg" />
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-heading" x-text="profile.name"></p>
                        <p class="truncate text-xs text-muted" x-text="profile.email"></p>
                    </div>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.name')"></label>
                        <input x-model="profile.name" class="{{ $field }}" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.email')"></label>
                        <input x-model="profile.email" class="{{ $field }}" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.role')"></label>
                        <input x-model="profile.role" class="{{ $field }}" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.bio')"></label>
                        <textarea rows="3" x-model="profile.bio" class="{{ $field }} resize-y"></textarea>
                    </div>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="button" @click="saveProfile()"
                        class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('common.save')"></button>
                </div>
            </div>

            {{-- Comunidade --}}
            <div x-show="tab === 1" x-cloak class="surface-card rounded-2xl border p-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.communityName')"></label>
                        <input x-model="community.name" class="{{ $field }}" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.tagline')"></label>
                        <input x-model="community.tagline" class="{{ $field }}" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.welcome')"></label>
                        <textarea rows="3" x-model="community.welcome" class="{{ $field }} resize-y"></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="{{ $row }}">
                        <span class="min-w-0">
                            <span class="block text-sm font-medium text-heading" x-text="$t('settings.publicRead')"></span>
                            <span class="block text-xs text-muted" x-text="$t('settings.publicReadDesc')"></span>
                        </span>
                        <span class="shrink-0">
                            <x-ui.switch-toggle checked="community.publicRead" toggle="community.publicRead = !community.publicRead" />
                        </span>
                    </label>
                </div>
                <div class="mt-5 flex justify-end">
                    <button type="button" @click="$store.ui.notifySuccess($t('settings.saved'))"
                        class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('common.save')"></button>
                </div>
            </div>

            {{-- Moderação --}}
            <div x-show="tab === 2" x-cloak class="surface-card rounded-2xl border p-5">
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.autoClose')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.autoCloseDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="moderation.autoClose" toggle="moderation.autoClose = !moderation.autoClose" />
                    </span>
                </label>
                <div x-show="moderation.autoClose" class="border-b border-themed py-4">
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.autoCloseDays')"></label>
                    <input type="number" min="7" max="365" x-model.number="moderation.autoCloseDays" class="{{ $field }} max-w-[160px]" />
                </div>
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.preApproval')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.preApprovalDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="moderation.preApproval" toggle="moderation.preApproval = !moderation.preApproval" />
                    </span>
                </label>
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.newMemberReview')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.newMemberReviewDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="moderation.newMemberReview" toggle="moderation.newMemberReview = !moderation.newMemberReview" />
                    </span>
                </label>
                <div class="py-4">
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('settings.minReputation')"></label>
                    <p class="mb-2 text-xs text-muted" x-text="$t('settings.minReputationDesc')"></p>
                    <input type="number" min="0" max="500" x-model.number="moderation.minReputationToLink" class="{{ $field }} max-w-[160px]" />
                </div>
                <div class="mt-2 flex justify-end">
                    <button type="button" @click="$store.ui.notifySuccess($t('settings.saved'))"
                        class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('common.save')"></button>
                </div>
            </div>

            {{-- Notificações --}}
            <div x-show="tab === 3" x-cloak class="surface-card rounded-2xl border p-5">
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.notifyReports')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.notifyReportsDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="notifications.reportQueue" toggle="notifications.reportQueue = !notifications.reportQueue" />
                    </span>
                </label>
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.notifyTopics')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.notifyTopicsDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="notifications.newTopic" toggle="notifications.newTopic = !notifications.newTopic" />
                    </span>
                </label>
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.notifyUnanswered')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.notifyUnansweredDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="notifications.unansweredDigest" toggle="notifications.unansweredDigest = !notifications.unansweredDigest" />
                    </span>
                </label>
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.notifyWeekly')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.notifyWeeklyDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="notifications.weeklyReport" toggle="notifications.weeklyReport = !notifications.weeklyReport" />
                    </span>
                </label>
            </div>

            {{-- Aparência --}}
            <div x-show="tab === 4" x-cloak class="surface-card rounded-2xl border p-5">
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.darkMode')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.darkModeDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <x-ui.switch-toggle checked="$store.ui.darkMode" toggle="$store.ui.toggleDarkMode()" />
                    </span>
                </label>
                <div class="border-b border-themed py-4">
                    <p class="text-sm font-medium text-heading" x-text="$t('settings.language')"></p>
                    <p class="text-xs text-muted" x-text="$t('settings.languageDesc')"></p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <template x-for="lang in $store.ui.languages" :key="lang.code">
                            <button type="button" @click="$store.ui.setLang(lang.code)"
                                class="flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition"
                                :class="$store.ui.lang === lang.code ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-themed text-body'">
                                <span aria-hidden x-text="lang.flag"></span>
                                <span x-text="lang.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <label class="{{ $row }}">
                    <span class="min-w-0">
                        <span class="block text-sm font-medium text-heading" x-text="$t('settings.cookies')"></span>
                        <span class="block text-xs text-muted" x-text="$t('settings.cookiesDesc')"></span>
                    </span>
                    <span class="shrink-0">
                        <button type="button" @click="$store.ui.openCookieBanner()"
                            class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body" x-text="$t('settings.cookiesButton')"></button>
                    </span>
                </label>
                <div class="py-4">
                    <p class="text-sm font-medium text-heading" x-text="$t('settings.resetTitle')"></p>
                    <p class="mt-0.5 text-xs text-muted" x-text="$t('settings.resetDesc')"></p>
                    <button type="button" @click="$store.forum.reset(); $store.ui.notifySuccess($t('settings.resetDone'))"
                        class="mt-3 inline-flex items-center gap-2 rounded-lg border border-themed px-4 py-2 text-sm font-medium text-error-600 transition hover:bg-error-50 dark:hover:bg-error-500/10">
                        <x-icon name="refresh-cw" :size="15" />
                        <span x-text="$t('settings.resetButton')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- O aviso de cookies também é reabrível a partir daqui. --}}
    <x-ui.cookie-banner />

    @push('scripts')
        <script>
            function settingsPage() {
                const user = $store.auth.user;
                return {
                    tab: 0,
                    profile: {
                        name: user ? user.name : '',
                        email: user ? user.email : '',
                        role: user ? user.role : '',
                        bio: $t('settings.defaultBio'),
                    },
                    community: {
                        name: 'Ágora',
                        tagline: $t('settings.defaultTagline'),
                        welcome: $t('settings.defaultWelcome'),
                        publicRead: true,
                    },
                    moderation: {
                        autoClose: true,
                        autoCloseDays: 60,
                        preApproval: false,
                        newMemberReview: true,
                        minReputationToLink: 25,
                    },
                    notifications: {
                        reportQueue: true,
                        newTopic: true,
                        unansweredDigest: true,
                        weeklyReport: false,
                    },
                    saveProfile() {
                        $store.auth.updateProfile({ name: this.profile.name, email: this.profile.email, role: this.profile.role });
                        $store.ui.notifySuccess($t('settings.saved'));
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
