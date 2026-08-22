{{-- Conteúdo comum do <head> + bootstrap de window.FO para todos os layouts. --}}
<meta charset="UTF-8" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.webmanifest" />
<meta name="theme-color" content="#465fff" />
<title>{{ isset($pageTitle) && $pageTitle ? $pageTitle . ' · ' : '' }}Ágora — Comunidade & Fórum</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
    // Dados canônicos compartilhados com os stores Alpine
    // (espelha forum/react src/data/mock.ts + src/stores).
    window.FO = {
        categories: @json(\App\Support\FoData::categories()),
        topics: @json(\App\Support\FoData::topics()),
        replies: @json(\App\Support\FoData::replies()),
        members: @json(\App\Support\FoData::members()),
        reports: @json(\App\Support\FoData::reports()),
        user: @json(session('user')),
        demo: { email: @json(\App\Support\MockUsers::DEMO_EMAIL), password: @json(\App\Support\MockUsers::DEMO_PASSWORD) },
    };
</script>
@stack('head')
