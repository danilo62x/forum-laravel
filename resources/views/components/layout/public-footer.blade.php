{{-- Mirrors forum/react components/layout/PublicFooter.tsx --}}
<footer class="surface-card border-t border-themed" x-data>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col items-start justify-between gap-8 lg:flex-row">
            <div class="max-w-sm">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white">
                        <x-icon name="message-square" :size="18" />
                    </span>
                    <span class="text-lg font-bold text-heading" data-i18n="brand" x-text="$t('brand')">Ágora</span>
                </a>
                <p class="mt-4 text-sm text-muted" data-i18n="landing.footer.tagline" x-text="$t('landing.footer.tagline')"></p>
            </div>
            <div class="grid min-w-0 grid-cols-2 gap-x-10 gap-y-2 text-sm sm:grid-cols-3">
                <template x-for="category in $store.forum.categories" :key="category.id">
                    <a :href="'/categoria/' + category.slug" class="truncate text-muted transition hover:text-brand-600" x-text="category.name"></a>
                </template>
                <a href="{{ route('search') }}" class="text-muted transition hover:text-brand-600" x-text="$t('nav.search')"></a>
                <a href="{{ route('rules') }}" class="text-muted transition hover:text-brand-600" x-text="$t('nav.rules')"></a>
            </div>
        </div>
        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-themed pt-6 sm:flex-row">
            <p class="text-xs text-muted">
                &copy; {{ date('Y') }} <span x-text="$t('brand')">Ágora</span>. <span x-text="$t('landing.footer.rights')"></span>
            </p>
            <div class="flex items-center gap-3">
                @foreach (['github', 'slack', 'globe'] as $icon)
                    <a href="#" aria-label="{{ $icon }}"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-themed text-muted transition hover:text-brand-500">
                        <x-icon :name="$icon" :size="16" />
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
