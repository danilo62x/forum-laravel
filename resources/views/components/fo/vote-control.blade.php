@props([
    'score' => '0',
    'vote' => 'null',
    'onVote' => '',
    'orientation' => 'vertical',
    'size' => 'md',
])
@php($box = $size === 'sm' ? 'h-7 w-7' : 'h-8 w-8')
@php($icon = $size === 'sm' ? 14 : 16)
@php($up = str_replace('%DIR%', "'up'", $onVote))
@php($down = str_replace('%DIR%', "'down'", $onVote))
{{-- Mirrors forum/react fo/VoteControl.tsx.
     Voto tri-estado: clicar de novo desfaz, clicar no oposto troca. O contador
     mostrado é sempre o score derivado, nunca um número que só sobe. --}}
<div x-data class="flex shrink-0 items-center gap-1.5 {{ $orientation === 'vertical' ? 'flex-col' : 'flex-row' }}">
    <button type="button" @click.stop.prevent="{{ $up }}"
        :aria-pressed="({{ $vote }}) === 'up'" :aria-label="$t('topic.upvote')" :title="$t('topic.upvote')"
        class="flex {{ $box }} items-center justify-center rounded-lg border transition"
        :class="({{ $vote }}) === 'up'
            ? 'border-success-500 bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'
            : 'border-themed text-muted hover:text-brand-500'">
        <x-icon name="chevron-up" :size="$icon" :strokeWidth="2.4" />
    </button>
    <span class="text-sm font-semibold tabular-nums"
        :class="({{ $vote }}) === 'up' ? 'text-success-600 dark:text-success-500' : (({{ $vote }}) === 'down' ? 'text-error-600 dark:text-error-500' : 'text-heading')"
        x-text="{{ $score }}"></span>
    <button type="button" @click.stop.prevent="{{ $down }}"
        :aria-pressed="({{ $vote }}) === 'down'" :aria-label="$t('topic.downvote')" :title="$t('topic.downvote')"
        class="flex {{ $box }} items-center justify-center rounded-lg border transition"
        :class="({{ $vote }}) === 'down'
            ? 'border-error-500 bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500'
            : 'border-themed text-muted hover:text-brand-500'">
        <x-icon name="chevron-down" :size="$icon" :strokeWidth="2.4" />
    </button>
</div>
