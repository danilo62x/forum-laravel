@props([
    'tabs' => [],
    'model' => 'tab',
])
{{-- Tab bar mirroring wiki/react ui/Tabs.tsx. Panels are rendered by the caller
     with x-show="{{ $model }} === i". Each tab: ['labelKey' => …, 'icon' => …] --}}
<div class="flex gap-1 overflow-x-auto border-b border-themed no-scrollbar" role="tablist">
    @foreach ($tabs as $i => $tab)
        <button type="button" role="tab" :aria-selected="{{ $model }} === {{ $i }}" @click="{{ $model }} = {{ $i }}"
            class="-mb-px inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition"
            :class="{{ $model }} === {{ $i }} ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-muted hover:border-gray-300 hover:text-body dark:hover:border-gray-700'">
            @isset($tab['icon'])<x-icon name="{{ $tab['icon'] }}" :size="16" />@endisset
            <span data-i18n="{{ $tab['labelKey'] }}" x-text="$t('{{ $tab['labelKey'] }}')"></span>
        </button>
    @endforeach
</div>
