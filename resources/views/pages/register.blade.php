<x-layout.blank :pageTitle="$pageTitle ?? null">
    @php($input = 'focus-ring h-11 w-full rounded-lg border border-themed bg-gray-50 px-3 text-sm text-body outline-none focus:border-brand-300 dark:bg-gray-900')
    <div x-data="{ loading: false, accepted: false }">
        <x-ui.auth-shell titleKey="auth.registerTitle" subtitleKey="auth.registerSubtitle">
            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4" @submit="loading = true">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.name')"></label>
                    <input name="name" required value="{{ old('name') }}" class="{{ $input }}" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.username')"></label>
                    <input name="organization" value="{{ old('organization') }}" class="{{ $input }}" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.email')"></label>
                    <input name="email" required type="email" value="{{ old('email') }}" class="{{ $input }}" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-heading" x-text="$t('auth.password')"></label>
                    <input name="password" required type="password" placeholder="••••••••" class="{{ $input }}" />
                </div>
                <label class="flex items-center gap-2 text-sm text-body">
                    <input type="checkbox" required x-model="accepted" class="h-4 w-4 rounded border-themed accent-brand-500" />
                    <span x-text="$t('auth.terms')"></span>
                </label>
                <button type="submit" :disabled="loading"
                    class="focus-ring flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-brand-500 text-sm font-medium text-white transition hover:bg-brand-600 disabled:opacity-60">
                    <span x-show="loading" x-cloak class="h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    <span x-show="!loading" class="flex items-center gap-2">
                        <x-icon name="check" :size="18" />
                        <span x-text="$t('auth.registerCta')"></span>
                    </span>
                </button>

                @if ($errors->any())
                    <ul class="rounded-lg bg-error-50 px-4 py-2 text-sm text-error-600 dark:bg-error-500/15 dark:text-error-500">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </form>

            <x-slot:footer>
                <span x-text="$t('auth.hasAccount')"></span>
                <a href="{{ route('login') }}" class="font-medium text-brand-500 hover:text-brand-600" x-text="$t('auth.loginLink')"></a>
            </x-slot:footer>
        </x-ui.auth-shell>
    </div>
</x-layout.blank>
