@props([
    'icon' => "'folder'",
    'size' => 20,
    'strokeWidth' => 1.8,
])
{{-- O nome do ícone da categoria é dado em runtime pelo store, então cada
     opção do seletor (fo/CategoriesPage ICONS) entra num x-if próprio. --}}
<span x-data class="contents">
    @foreach (['megaphone', 'help', 'code', 'palette', 'server', 'coffee', 'star', 'zap', 'folder', 'flag'] as $name)
        <template x-if="({{ $icon }}) === '{{ $name }}'">
            <x-icon :name="$name" :size="$size" :strokeWidth="$strokeWidth" {{ $attributes }} />
        </template>
    @endforeach
</span>
