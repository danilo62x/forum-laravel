@props(['pageTitle' => null])
<!DOCTYPE html>
<html lang="pt-BR" :class="{ 'dark': $store.ui.darkMode }" x-data>
<head>
    @include('layouts.partials-head')
</head>
<body>
    <div class="flex min-h-screen flex-col overflow-x-hidden" style="background-color: var(--tx-body-bg)" x-data>
        <x-layout.public-navbar />

        <main class="flex-1">
            {{ $slot }}
        </main>

        <x-layout.public-footer />
        <x-ui.toast-stack />
        <x-ui.cookie-banner />
    </div>
    @stack('scripts')
</body>
</html>
