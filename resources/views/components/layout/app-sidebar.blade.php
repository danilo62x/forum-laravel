@php($sections = [
    ['titleKey' => 'appNav.communitySection', 'items' => [
        ['href' => route('dashboard'), 'labelKey' => 'appNav.dashboard', 'icon' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        ['href' => route('app.topics'), 'labelKey' => 'appNav.topics', 'icon' => 'message-square', 'active' => request()->routeIs('app.topics') || request()->routeIs('app.topic-detail')],
        ['href' => route('app.categories'), 'labelKey' => 'appNav.categories', 'icon' => 'folder', 'active' => request()->routeIs('app.categories')],
    ]],
    ['titleKey' => 'appNav.moderationSection', 'items' => [
        ['href' => route('app.reports'), 'labelKey' => 'appNav.reports', 'icon' => 'flag', 'active' => request()->routeIs('app.reports'), 'badge' => true],
        ['href' => route('app.members'), 'labelKey' => 'appNav.members', 'icon' => 'users', 'active' => request()->routeIs('app.members')],
    ]],
    ['titleKey' => 'appNav.settingsSection', 'items' => [
        ['href' => route('app.settings'), 'labelKey' => 'appNav.settings', 'icon' => 'settings', 'active' => request()->routeIs('app.settings')],
    ]],
])
{{-- Mirrors forum/react components/layout/AppSidebar.tsx --}}
<aside x-data="{ get pending() { return foHelpers.openReports($store.forum.reports).length } }"
    :style="{ width: $store.ui.sidebarCollapsed ? '90px' : '280px' }"
    class="surface-sidebar fixed top-0 left-0 z-50 flex h-screen flex-col overflow-y-hidden border-r px-4 transition-[transform,width] duration-300 lg:static lg:translate-x-0"
    :class="$store.ui.sidebarMobileOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex items-center pt-7 pb-6" :class="$store.ui.sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2.5">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-500 text-white shadow-theme-sm">
                <x-icon name="message-square" :size="18" />
            </span>
            <span x-show="!$store.ui.sidebarCollapsed" data-brand-text class="truncate text-lg font-semibold" x-text="$t('brand')">Ágora</span>
        </a>
        <button type="button" x-show="!$store.ui.sidebarCollapsed" @click="$store.ui.toggleSidebar()" :title="$t('appNav.collapse')"
            class="is-sidebar-inactive hidden h-8 w-8 items-center justify-center rounded-lg border border-themed lg:flex">
            <x-icon name="chevron-right" :size="14" class="rotate-180" />
        </button>
    </div>

    <div class="no-scrollbar flex flex-col gap-5 overflow-y-auto pb-4">
        @foreach ($sections as $section)
            <nav>
                <h3 x-show="!$store.ui.sidebarCollapsed" class="mb-2 px-2 text-[11px] font-semibold tracking-wider uppercase"
                    data-i18n="{{ $section['titleKey'] }}" x-text="$t('{{ $section['titleKey'] }}')"></h3>
                <ul class="flex flex-col gap-1">
                    @foreach ($section['items'] as $item)
                        <li>
                            <a href="{{ $item['href'] }}" @click="$store.ui.toggleSidebarMobile(false)"
                                :title="$store.ui.sidebarCollapsed ? $t('{{ $item['labelKey'] }}') : null"
                                class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $item['active'] ? 'is-sidebar-active' : 'is-sidebar-inactive' }}"
                                :class="$store.ui.sidebarCollapsed ? 'justify-center' : ''">
                                <x-icon :name="$item['icon']" :size="20" />
                                <span x-show="!$store.ui.sidebarCollapsed" class="min-w-0 flex-1 truncate"
                                    data-i18n="{{ $item['labelKey'] }}" x-text="$t('{{ $item['labelKey'] }}')"></span>
                                @if (!empty($item['badge']))
                                    <span x-show="!$store.ui.sidebarCollapsed && pending > 0" x-cloak
                                        class="rounded-full bg-error-500 px-2 py-0.5 text-[11px] font-semibold text-white" x-text="pending"></span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endforeach
    </div>

    <div class="mt-auto space-y-3 border-t border-themed py-4">
        <div x-show="!$store.ui.sidebarCollapsed" class="rounded-xl bg-brand-50 p-4 dark:bg-brand-500/10">
            <p class="text-xs font-medium text-muted" data-i18n="appNav.queueTitle" x-text="$t('appNav.queueTitle')"></p>
            <p class="mt-0.5 text-sm font-semibold text-heading" x-text="$t('appNav.queueDesc', { count: pending })"></p>
            <a href="{{ route('app.reports') }}"
                class="mt-3 flex h-9 w-full items-center justify-center rounded-lg bg-brand-500 text-xs font-medium text-white transition hover:bg-brand-600"
                x-text="$t('appNav.openQueue')"></a>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" :title="$store.ui.sidebarCollapsed ? $t('appNav.logout') : null"
                class="is-sidebar-inactive group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                :class="$store.ui.sidebarCollapsed ? 'justify-center' : ''">
                <x-icon name="logout" :size="20" />
                <span x-show="!$store.ui.sidebarCollapsed" x-text="$t('appNav.logout')"></span>
            </button>
        </form>
    </div>
</aside>
