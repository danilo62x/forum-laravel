@props([
    'title' => '',
    'titleText' => null,
    'subtitle' => null,
    'subtitleText' => null,
    'crumbs' => [],
])
{{-- $title / $subtitle são chaves de i18n resolvidas reativamente por $t.
     $titleText / $subtitleText são expressões Alpine cruas (ex.: o slug da página). --}}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" x-data>
    <div>
        @if (count($crumbs))
            <div class="mb-2"><x-ui.breadcrumb :items="$crumbs" /></div>
        @endif
        @if ($titleText)
            <h1 class="text-2xl font-bold text-heading" x-text="{{ $titleText }}"></h1>
        @else
            <h1 class="text-2xl font-bold text-heading" data-i18n="{{ $title }}" x-text="$t('{{ $title }}')"></h1>
        @endif
        @if ($subtitle)
            <p class="mt-1 text-sm text-muted" data-i18n="{{ $subtitle }}" x-text="$t('{{ $subtitle }}')"></p>
        @elseif ($subtitleText)
            <p class="mt-1 text-sm text-muted" x-text="{{ $subtitleText }}"></p>
        @endif
    </div>
    @isset($action)
        <div class="shrink-0">{{ $action }}</div>
    @endisset
</div>
