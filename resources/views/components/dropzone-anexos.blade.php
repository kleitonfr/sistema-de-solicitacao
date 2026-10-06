@props([
    'nome',
    'rotulo',
    'aceitos',
    'ajuda',
    'cor' => 'verde',
    'multiplo' => false,
    'maximoDeArquivos' => 1,
    'invalido' => false,
])

@php
    $idDaInstrucao = "{$nome}-instrucao";
@endphp

<div
    class="dropzone dropzone--{{ $cor }} {{ $invalido ? 'dropzone--invalido' : '' }}"
    data-dropzone
    data-maximo-arquivos="{{ $multiplo ? $maximoDeArquivos : 1 }}"
>
    <input
        type="file"
        id="{{ $nome }}"
        name="{{ $multiplo ? "{$nome}[]" : $nome }}"
        class="dropzone__input"
        accept="{{ $aceitos }}"
        aria-label="{{ $rotulo }}"
        aria-describedby="{{ $idDaInstrucao }}"
        @if ($multiplo) multiple @endif
    >

    <div class="dropzone__vazio" data-dropzone-vazio>
        <i class="fa-solid fa-cloud-arrow-up dropzone__icone" aria-hidden="true"></i>
        <p class="dropzone__texto" id="{{ $idDaInstrucao }}">
            Arraste {{ $multiplo ? 'arquivos' : 'um arquivo' }} aqui ou
            <span class="dropzone__link">clique para selecionar</span>
        </p>
        <p class="dropzone__ajuda">{{ $ajuda }}</p>
    </div>

    <ul class="dropzone__lista" data-dropzone-lista hidden></ul>
    <p class="dropzone__aviso" data-dropzone-aviso role="status" hidden></p>
</div>
