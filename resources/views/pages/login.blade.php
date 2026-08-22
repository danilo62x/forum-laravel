<x-layout.blank>
    <div x-data="{ showPassword: false, remember: true, loading: false }">
        <x-ui.auth-shell titleKey="auth.loginTitle" subtitleKey="auth.loginSubtitle">
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-5" @submit="loading = true">
                @csrf
                <input type="hidden" name="redirect" value="{{ request()->query('redirect', '') }}" />

                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" for="email" x-text="$t('auth.email')"></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <x-icon name="mail" :size="18" />
                        </span>
                        <input id="email" name="email" type="email" required
                            value="{{ old('email', \App\Support\MockUsers::DEMO_EMAIL) }}"
                            data-i18n-ph="auth.emailPlaceholder" :placeholder="$t('auth.emailPlaceholder')"
                            class="focus-ring h-11 w-full rounded-lg border border-themed bg-gray-50 pl-10 pr-3 text-sm text-body outline-none focus:border-brand-300 dark:bg-gray-900" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" for="password" x-text="$t('auth.password')"></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <x-icon name="lock" :size="18" />
                        </span>
                        <input id="password" name="password" required :type="showPassword ? 'text' : 'password'"
                            value="{{ \App\Support\MockUsers::DEMO_PASSWORD }}" placeholder="••••••••"
                            class="focus-ring h-11 w-full rounded-lg border border-themed bg-gray-50 pl-10 pr-10 text-sm text-body outline-none focus:border-brand-300 dark:bg-gray-900" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <template x-if="showPassword"><x-icon name="eye-off" :size="18" /></template>
                            <template x-if="!showPassword"><x-icon name="eye" :size="18" /></template>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-body">
                        <input type="checkbox" x-model="remember" class="h-4 w-4 rounded border-themed accent-brand-500" />
                        <span x-text="$t('auth.remember')"></span>
                    </label>
                    <span class="font-medium text-brand-500" x-text="$t('auth.forgot')"></span>
                </div>

                <button type="submit" :disabled="loading"
                    class="focus-ring flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60">
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                        <span x-text="$t('auth.loggingIn')"></span>
                    </span>
                    <span x-show="!loading" x-text="$t('auth.loginCta')"></span>
                </button>

                @if ($errors->any())
                    <p class="rounded-lg bg-error-50 px-4 py-2 text-center text-sm text-error-600 dark:bg-error-500/15 dark:text-error-500"
                        x-text="$t('auth.loginError')"></p>
                @endif
            </form>

            <div class="mt-6 rounded-lg border border-dashed border-themed bg-gray-50/60 p-3 text-xs text-muted dark:bg-gray-900/40">
                <p class="font-medium text-body" x-text="$t('auth.demoTitle')"></p>
                <p>{{ \App\Support\MockUsers::DEMO_EMAIL }} / {{ \App\Support\MockUsers::DEMO_PASSWORD }}</p>
                <p class="mt-0.5" x-text="$t('auth.demoHint')"></p>
            </div>

            <x-slot:footer>
                <span x-text="$t('auth.noAccount')"></span>
                <a href="{{ route('register') }}" class="font-medium text-brand-500 hover:text-brand-600" x-text="$t('auth.registerCta')"></a>
            </x-slot:footer>
        </x-ui.auth-shell>
    </div>
</x-layout.blank>
