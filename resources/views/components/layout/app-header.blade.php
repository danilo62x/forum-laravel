{{-- Mirrors forum/react components/layout/AppHeader.tsx --}}
<header class="surface-header sticky top-0 z-30 flex w-full border-b"
    x-data="{
        dropdownOpen: false,
        notifOpen: false,
        notifications: [
            { id: 1, key: 'newReport', name: 'Bate-papo', timeKey: 'minutes', read: false },
            { id: 2, key: 'unanswered', name: 'Dúvidas e suporte', timeKey: 'hours', read: false },
            { id: 3, key: 'newMember', name: 'Sofia Andrade', timeKey: 'yesterday', read: true },
        ],
        get initials() {
            const user = $store.auth.user;
            if (!user) return '?';
            return user.name.split(' ').slice(0, 2).map(w => (w[0] || '').toUpperCase()).join('');
        },
        toggleSidebar() {
            if (window.innerWidth < 1024) $store.ui.toggleSidebarMobile();
            else $store.ui.toggleSidebar();
        },
    }">
    <div class="flex flex-grow items-center justify-between gap-4 px-4 py-3 md:px-6 2xl:px-10">
        <div class="flex items-center gap-3">
            <button type="button" @click="toggleSidebar()" aria-label="Toggle sidebar"
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-themed text-muted hover:bg-gray-50 dark:hover:bg-gray-800">
                <x-icon name="menu" :size="20" />
            </button>

            <a href="{{ route('app.topics', ['novo' => 1]) }}"
                class="hidden h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600 sm:flex">
                <x-icon name="plus" :size="18" />
                <span x-text="$t('topicsAdmin.new')"></span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('search') }}"
                class="hidden h-10 items-center gap-2 rounded-lg border border-themed px-4 text-sm text-muted transition hover:bg-gray-50 md:flex dark:hover:bg-gray-800">
                <x-icon name="search" :size="16" />
                <span x-text="$t('common.search')"></span>
            </a>
            <x-ui.language-switcher />
            <button type="button" @click="$store.ui.toggleDarkMode()" aria-label="Toggle dark mode"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-themed text-muted hover:bg-gray-50 dark:hover:bg-gray-800">
                <template x-if="$store.ui.darkMode"><x-icon name="sun" :size="18" /></template>
                <template x-if="!$store.ui.darkMode"><x-icon name="moon" :size="18" /></template>
            </button>

            <div class="relative" @click.outside="notifOpen = false">
                <button type="button" @click="notifOpen = !notifOpen" :aria-label="$t('appNav.notifications')"
                    class="relative flex h-10 w-10 items-center justify-center rounded-full border border-themed text-muted hover:bg-gray-50 dark:hover:bg-gray-800">
                    <x-icon name="bell" :size="18" />
                    <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-error-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-error-500"></span>
                    </span>
                </button>
                <div x-show="notifOpen" x-cloak x-transition
                    class="surface-card absolute right-0 z-50 mt-3 w-72 max-w-[calc(100vw-2rem)] rounded-xl border shadow-theme-lg sm:w-80">
                    <div class="flex items-center justify-between border-b border-themed px-4 py-3">
                        <p class="text-sm font-semibold text-heading" x-text="$t('appNav.notifications')"></p>
                    </div>
                    <ul class="max-h-72 divide-y divide-themed overflow-y-auto">
                        <template x-for="n in notifications" :key="n.id">
                            <li class="flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <span class="mt-1 flex h-2 w-2 shrink-0 rounded-full" :class="n.read ? 'bg-gray-300' : 'bg-brand-500'"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-heading" x-text="$t('notifications.' + n.key, { name: n.name })"></p>
                                    <p class="text-xs text-muted" x-text="$t('notifications.' + n.timeKey)"></p>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="relative" @click.outside="dropdownOpen = false">
                <button type="button" class="ml-1 flex items-center gap-3" @click="dropdownOpen = !dropdownOpen">
                    <span class="hidden text-right lg:block">
                        <span class="block text-sm font-medium text-heading" x-text="$store.auth.user?.name || $t('common.guest')"></span>
                        <span class="block text-xs text-muted" x-text="$store.auth.user?.role || '—'"></span>
                    </span>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 font-semibold text-white" x-text="initials"></span>
                    <x-icon name="chevron-down" :size="16" class="text-muted" />
                </button>

                <div x-show="dropdownOpen" x-cloak x-transition class="surface-card absolute right-0 z-50 mt-3 w-56 rounded-xl border shadow-theme-lg">
                    <div class="border-b border-themed px-4 py-3">
                        <p class="truncate text-sm font-medium text-heading" x-text="$store.auth.user?.name || $t('common.guest')"></p>
                        <p class="truncate text-xs text-muted" x-text="$store.auth.user?.email || ''"></p>
                    </div>
                    <ul class="py-1">
                        <li>
                            <a href="{{ route('app.settings') }}" @click="dropdownOpen = false"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-body hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="settings" :size="16" /> <span x-text="$t('appNav.settings')"></span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}" @click="dropdownOpen = false"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-body hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="globe" :size="16" /> <span x-text="$t('appNav.viewSite')"></span>
                            </a>
                        </li>
                    </ul>
                    <div class="border-t border-themed py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2 px-4 py-2 text-sm text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10">
                                <x-icon name="logout" :size="16" /> <span x-text="$t('appNav.logout')"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
