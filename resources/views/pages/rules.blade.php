<x-layout.public :pageTitle="$pageTitle ?? null">
    @php($sections = [
        ['key' => 's1', 'icon' => 'users'],
        ['key' => 's2', 'icon' => 'message-square'],
        ['key' => 's3', 'icon' => 'flag'],
        ['key' => 's4', 'icon' => 'shield-check'],
        ['key' => 's5', 'icon' => 'award'],
    ])
    @php($faq = ['q1', 'q2', 'q3', 'q4', 'q5', 'q6'])

    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8" x-data="{ open: 'q1' }">
        <x-ui.breadcrumb :items="[
            ['labelKey' => 'nav.home', 'href' => route('home'), 'icon' => 'grid'],
            ['labelKey' => 'rules.title'],
        ]" />

        <header class="mt-5">
            <h1 class="text-2xl font-bold text-heading sm:text-title-sm" data-i18n="rules.title" x-text="$t('rules.title')"></h1>
            <p class="mt-3 text-sm text-muted" data-i18n="rules.subtitle" x-text="$t('rules.subtitle')"></p>
            <p class="mt-2 text-xs text-muted" data-i18n="rules.updated" x-text="$t('rules.updated')"></p>
        </header>

        <div class="mt-8 space-y-5">
            @foreach ($sections as $index => $section)
                <section class="surface-card rounded-2xl border p-5 md:p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                            <x-icon :name="$section['icon']" :size="20" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-lg font-semibold text-heading">
                                {{ $index + 1 }}. <span x-text="$t('rules.sections.{{ $section['key'] }}.title')"></span>
                            </h2>
                            <p class="mt-2 text-sm leading-relaxed text-body" x-text="$t('rules.sections.{{ $section['key'] }}.p1')"></p>
                            <p class="mt-2 text-sm leading-relaxed text-body" x-text="$t('rules.sections.{{ $section['key'] }}.p2')"></p>
                            <p class="mt-2 text-sm leading-relaxed text-body" x-text="$t('rules.sections.{{ $section['key'] }}.p3')"></p>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>

        <section class="mt-10">
            <h2 class="text-xl font-bold text-heading" data-i18n="rules.faqTitle" x-text="$t('rules.faqTitle')"></h2>
            <p class="mt-1 text-sm text-muted" data-i18n="rules.faqSubtitle" x-text="$t('rules.faqSubtitle')"></p>

            <div class="mt-5 space-y-2">
                @foreach ($faq as $key)
                    <div class="surface-card overflow-hidden rounded-xl border">
                        <button type="button" @click="open = open === '{{ $key }}' ? null : '{{ $key }}'"
                            :aria-expanded="open === '{{ $key }}'"
                            class="flex w-full items-center justify-between gap-3 px-5 py-4 text-left">
                            <span class="min-w-0 text-sm font-medium text-heading" x-text="$t('rules.faq.{{ $key }}.q')"></span>
                            <x-icon name="chevron-down" :size="18" class="shrink-0 text-muted transition-transform"
                                ::class="open === '{{ $key }}' ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open === '{{ $key }}'" x-collapse>
                            <div class="border-t border-themed px-5 py-4 text-sm leading-relaxed text-body" x-text="$t('rules.faq.{{ $key }}.a')"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-10 rounded-2xl bg-brand-500 p-6 text-white sm:p-8">
            <h2 class="text-lg font-semibold" data-i18n="rules.ctaTitle" x-text="$t('rules.ctaTitle')"></h2>
            <p class="mt-1 text-sm text-white/80" data-i18n="rules.ctaDesc" x-text="$t('rules.ctaDesc')"></p>
            <a href="{{ route('search') }}"
                class="mt-4 inline-flex h-10 items-center rounded-lg bg-white px-4 text-sm font-medium text-brand-600 transition hover:bg-brand-50"
                x-text="$t('rules.ctaButton')"></a>
        </section>
    </div>
</x-layout.public>
