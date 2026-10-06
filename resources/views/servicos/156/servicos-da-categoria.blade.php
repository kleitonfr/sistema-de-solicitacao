@extends('layouts.app')

@section('title', $categoria->nome.' — Portal 156 — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="categorias-servico-page">
        <div class="container">

            <a href="{{ route('servicos.156.categorias') }}" class="categorias-servico-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para as categorias
            </a>

            <div class="categorias-servico-cabecalho">
                <h1 class="categorias-servico-cabecalho__titulo">{{ $categoria->nome }}</h1>
                <p class="categorias-servico-cabecalho__subtitulo">{{ $categoria->descricao }}</p>
            </div>

            <p class="categorias-servico-resultado" role="status">
                {{ trans_choice(':total serviço|:total serviços', $servicos->count(), ['total' => $servicos->count()]) }}
            </p>

            @if ($servicos->isEmpty())
                <div class="categorias-servico-vazio">
                    <i class="fa-regular fa-folder-open categorias-servico-vazio__icone" aria-hidden="true"></i>
                    <p class="categorias-servico-vazio__texto">Ainda não há serviços disponíveis nesta categoria.</p>
                    <a href="{{ route('servicos.156.categorias') }}" class="categorias-servico-vazio__acao">
                        Escolher outra categoria
                    </a>
                </div>
            @else
                <div class="categorias-servico-grade">
                    @foreach ($servicos as $servico)
                        <a href="{{ route('servicos.156.solicitacao.criar', $servico->slug) }}" class="categoria-servico">
                            <span class="categoria-servico__icone">
                                <i class="{{ $categoria->icone() }}" aria-hidden="true"></i>
                            </span>
                            <h2 class="categoria-servico__nome">{{ $servico->nome }}</h2>
                            <p class="categoria-servico__descricao">{{ $servico->descricao }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
@endsection
