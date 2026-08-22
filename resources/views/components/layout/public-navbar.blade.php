@php($links = [
    ['href' => route('home'), 'key' => 'nav.home', 'active' => request()->routeIs('home')],
    ['href' => route('search'), 'key' => 'nav.search', 'active' => request()->routeIs('search')],
    ['href' => route('rules'), 'key' => 'nav.rules', 'active' => request()->routeIs('rules')],
])
{{-- Mirrors forum/react components/layout/PublicNavbar.tsx --}}
<header x-data="{ mobileOpen: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 8"
    class="sticky top-0 z-40 border-b transition-colors"
    :class="scrolled ? 'surface-header border-themed backdrop-blur' : 'border-transparent bg-transparent'">
    <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-500 text-white shadow-theme-md">
                <x-icon name="message-square" :size="18" />
            </span>
            <span class="truncate text-lg font-bold text-heading" data-i18n="brand" x-text="$t('brand')">Ágora</span>
        </a>

        <nav class="ml-6 hidden items-center gap-1 lg:flex">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                    class="rounded-lg px-3 py-2 text-sm font-medium transition {{ $link['active'] ? 'text-brand-600 dark:text-brand-400' : 'text-body hover:text-heading' }}"
                    data-i18n="{{ $link['key'] }}" x-text="$t('{{ $link['key'] }}')"></a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-1 sm:gap-2">
            <a href="{{ route('search') }}" :aria-label="$t('nav.search')"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-themed text-body transition hover:bg-gray-50 lg:hidden dark:hover:bg-gray-800">
                <x-icon name="search" :size="18" />
            </a>
            <button type="button" @click="$store.ui.toggleDarkMode()" :aria-label="$t('common.theme')"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-themed text-body transition hover:bg-gray-50 dark:hover:bg-gray-800">
                <template x-if="$store.ui.darkMode"><x-icon name="sun" :size="18" /></template>
                <template x-if="!$store.ui.darkMode"><x-icon name="moon" :size="18" /></template>
            </button>
            <x-ui.language-switcher />

            <template x-if="$store.auth.isAuthenticated">
                <a href="{{ route('dashboard') }}"
                    class="hidden h-10 items-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600 sm:flex"
                    x-text="$t('nav.dashboard')"></a>
            </template>
            <template x-if="!$store.auth.isAuthenticated">
                <span class="flex items-center gap-2">
                    <a href="{{ route('login') }}"
                        class="hidden h-10 items-center rounded-lg border border-themed px-4 text-sm font-medium text-body transition hover:bg-gray-50 sm:flex dark:hover:bg-gray-800"
                        x-text="$t('nav.login')"></a>
                    <a href="{{ route('register') }}"
                        class="hidden h-10 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600 sm:flex">
                        <x-icon name="user" :size="16" />
                        <span x-text="$t('nav.join')"></span>
                    </a>
                </span>
            </template>

            <button type="button" @click="mobileOpen = !mobileOpen" aria-label="Menu"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-body lg:hidden">
                <template x-if="mobileOpen"><x-icon name="x" :size="22" /></template>
                <template x-if="!mobileOpen"><x-icon name="menu" :size="22" /></template>
            </button>
        </div>
    </div>

    <div x-show="mobileOpen" x-cloak x-transition class="surface-card border-t border-themed lg:hidden">
        <nav class="flex flex-col gap-1 px-4 py-4 sm:px-6">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                    class="rounded-lg px-3 py-2.5 text-sm font-medium text-body hover:bg-gray-50 dark:hover:bg-gray-800"
                    data-i18n="{{ $link['key'] }}" x-text="$t('{{ $link['key'] }}')"></a>
            @endforeach
            <div class="mt-2 flex flex-col gap-2 border-t border-themed pt-3">
                <template x-if="$store.auth.isAuthenticated">
                    <a href="{{ route('dashboard') }}"
                        class="flex h-11 items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white"
                        x-text="$t('nav.dashboard')"></a>
                </template>
                <template x-if="!$store.auth.isAuthenticated">
                    <span class="flex flex-col gap-2">
                        <a href="{{ route('login') }}"
                            class="flex h-11 items-center justify-center rounded-lg border border-themed text-sm font-medium text-body"
                            x-text="$t('nav.login')"></a>
                        <a href="{{ route('register') }}"
                            class="flex h-11 items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white"
                            x-text="$t('nav.join')"></a>
                    </span>
                </template>
            </div>
        </nav>
    </div>
</header>
