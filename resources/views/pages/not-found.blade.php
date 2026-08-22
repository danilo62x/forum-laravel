<x-layout.blank :pageTitle="$pageTitle ?? null">
    {{-- Mirrors forum/react pages/app/NotFoundPage.tsx --}}
    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center"
        style="background-color: var(--tx-body-bg)" x-data>
        <span class="flex h-20 w-20 items-center justify-center rounded-3xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
            <x-icon name="message-square" :size="40" />
        </span>
        <p class="mt-8 text-title-lg font-bold text-heading">404</p>
        <h1 class="mt-2 text-xl font-semibold text-heading" data-i18n="notFound.title" x-text="$t('notFound.title')"></h1>
        <p class="mt-2 max-w-md text-sm text-muted" data-i18n="notFound.desc" x-text="$t('notFound.desc')"></p>
        <a :href="$store.auth.isAuthenticated ? '{{ route('dashboard') }}' : '{{ route('home') }}'"
            class="mt-8 flex h-11 items-center gap-2 rounded-lg bg-brand-500 px-6 text-sm font-medium text-white transition hover:bg-brand-600">
            <x-icon name="arrow-right" :size="16" />
            <span x-text="$store.auth.isAuthenticated ? $t('notFound.back') : $t('notFound.home')"></span>
        </a>
    </div>
</x-layout.blank>
