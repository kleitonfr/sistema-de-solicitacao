@props(['tipo' => 'sucesso'])

@php
    $icone = $tipo === 'sucesso' ? 'fa-circle-check' : 'fa-circle-exclamation';
    $role = $tipo === 'sucesso' ? 'status' : 'alert';
    $autoFechar = $tipo === 'sucesso';
@endphp

<div
    {{ $attributes->merge(['class' => "cadastro-alert cadastro-alert--{$tipo}"]) }}
    role="{{ $role }}"
    @if ($autoFechar) data-fecha-automatico @endif
>
    <i class="fa-solid {{ $icone }} cadastro-alert__icone" aria-hidden="true"></i>
    <span>{{ $slot }}</span>
</div>
