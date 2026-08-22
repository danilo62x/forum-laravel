@props(['text' => "''", 'clamp' => 'undefined'])
{{-- Mirrors forum/react fo/RichBody.tsx. O HTML é montado por foHelpers.richBody,
     que escapa tudo antes de aplicar negrito, código, lista, citação e bloco. --}}
<div x-data {{ $attributes->merge(['class' => 'min-w-0 break-words']) }} x-html="foHelpers.richBody({{ $text }}, {{ $clamp }})"></div>
