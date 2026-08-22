{{-- Mirrors forum/react ui/CookieBanner.tsx — z-[60], acima do conteúdo e abaixo do modal. --}}
<div x-data x-show="$store.ui.showCookieBanner" x-transition x-cloak class="fixed inset-x-0 bottom-0 z-[60] p-4">
    <div class="surface-card mx-auto flex max-w-3xl flex-col gap-4 rounded-2xl border p-5 shadow-theme-xl sm:flex-row sm:items-center">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
            <x-icon name="cookie" :size="22" />
        </span>
        <div class="flex-1">
            <p class="text-sm font-semibold text-heading" x-text="$t('cookie.title')"></p>
            <p class="mt-0.5 text-xs text-muted">
                <span x-text="$t('cookie.desc')"></span>
                <a href="#" class="font-medium text-brand-500 hover:text-brand-600" x-text="$t('cookie.policy')"></a>
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <button type="button" @click="$store.ui.declineCookies()"
                class="focus-ring rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800"
                x-text="$t('cookie.decline')"></button>
            <button type="button" @click="$store.ui.acceptCookies()"
                class="focus-ring rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600"
                x-text="$t('cookie.accept')"></button>
        </div>
    </div>
</div>
