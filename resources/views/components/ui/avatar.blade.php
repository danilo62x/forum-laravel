@props([
    'src' => 'null',
    'name' => "''",
    'size' => 'md',
])
@php($sizes = [
    'xs' => 'h-7 w-7 text-[10px]',
    'sm' => 'h-9 w-9 text-xs',
    'md' => 'h-11 w-11 text-sm',
    'lg' => 'h-14 w-14 text-base',
    'xl' => 'h-16 w-16 text-lg',
])
{{-- Mirrors forum/react ui/Avatar.tsx. $src / $name são expressões Alpine. --}}
<span x-data class="relative inline-flex shrink-0">
    <template x-if="{{ $src }}">
        <img :src="{{ $src }}" :alt="{{ $name }}" loading="lazy" class="rounded-full object-cover {{ $sizes[$size] ?? $sizes['md'] }}" />
    </template>
    <template x-if="!({{ $src }})">
        <span class="flex items-center justify-center rounded-full bg-brand-500 font-bold text-white {{ $sizes[$size] ?? $sizes['md'] }}"
            x-text="(({{ $name }}) || '?').trim().split(/\s+/).slice(0, 2).map(p => (p[0] || '').toUpperCase()).join('')"></span>
    </template>
</span>
