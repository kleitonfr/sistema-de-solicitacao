@props(['status'])

@php
    $iconesDosStatus = [
        'recebida' => 'fa-inbox',
        'em_analise' => 'fa-magnifying-glass',
        'encaminhada' => 'fa-share',
        'em_atendimento' => 'fa-screwdriver-wrench',
        'concluida' => 'fa-circle-check',
    ];
@endphp

{{-- O status é sempre escrito: a cor e o ícone só reforçam. --}}
<span class="status-da-solicitacao status-da-solicitacao--{{ $status }}">
    <i class="fa-solid {{ $iconesDosStatus[$status] ?? 'fa-circle' }}" aria-hidden="true"></i>
    {{ \App\Models\MovimentacaoDaSolicitacao::rotuloDoStatus($status) }}
</span>
