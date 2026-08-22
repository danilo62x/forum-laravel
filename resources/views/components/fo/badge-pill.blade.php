@props(['name' => "''", 'size' => 'md'])
@php($pad = $size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs')
@php($iconSize = $size === 'sm' ? 11 : 13)
{{-- Mirrors forum/react fo/BadgePill.tsx. A lista de distintivos vem sempre
     calculada por limiar no store — nunca escrita à mão. --}}
<span x-data :title="$t('badges.' + ({{ $name }}) + '.desc')"
    class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400 {{ $pad }}">
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'shield-check'"><x-icon name="shield-check" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'clock'"><x-icon name="clock" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'message-square'"><x-icon name="message-square" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'message-circle'"><x-icon name="message-circle" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'layers'"><x-icon name="layers" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'check-circle'"><x-icon name="check-circle" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'star'"><x-icon name="star" :size="$iconSize" :strokeWidth="2.2" /></template>
    <template x-if="foHelpers.badgeIcon({{ $name }}) === 'award'"><x-icon name="award" :size="$iconSize" :strokeWidth="2.2" /></template>
    <span x-text="$t('badges.' + ({{ $name }}) + '.label')"></span>
</span>
