@extends('layouts.app')

@section('title', $detalhe->resumo->titulo.' — Minhas solicitações — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="minhas-solicitacoes-page">
        <div class="container">

            <a href="{{ route('minhas-solicitacoes.index') }}" class="minhas-solicitacoes-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para Minhas solicitações
            </a>

            <div class="minhas-solicitacoes-cabecalho">
                <span class="minhas-solicitacao__tipo tipo-{{ $detalhe->resumo->chaveDoTipo }}">{{ $detalhe->resumo->rotuloDoTipo }}</span>
                <h1 class="minhas-solicitacoes-cabecalho__titulo">{{ $detalhe->resumo->titulo }}</h1>
                <p class="minhas-solicitacoes-cabecalho__subtitulo">
                    Protocolo <strong>{{ $detalhe->resumo->protocolo }}</strong>
                    · Registrada em
                    <time datetime="{{ $detalhe->resumo->registradaEm->toDateString() }}">{{ $detalhe->resumo->registradaEm->format('d/m/Y') }}</time>
                </p>
                <p class="minhas-solicitacoes-cabecalho__situacao">
                    Situação atual:
                    <x-status-da-solicitacao :status="$detalhe->resumo->status" />
                </p>
            </div>

            @if (session('erro_geral'))
                <x-cadastro-alerta tipo="erro">{{ session('erro_geral') }}</x-cadastro-alerta>
            @endif

            <section class="minhas-solicitacoes-secao" aria-labelledby="titulo-andamento">
                <h2 id="titulo-andamento" class="minhas-solicitacoes-secao__titulo">Andamento</h2>

                @if ($detalhe->movimentacoes->isEmpty())
                    <p class="minhas-solicitacoes-secao__vazio">Ainda não há movimentações registradas.</p>
                @else
                    <ol class="linha-do-tempo">
                        @foreach ($detalhe->movimentacoes as $movimentacao)
                            <li class="linha-do-tempo__item {{ $loop->last ? 'linha-do-tempo__item--atual' : '' }}" @if ($loop->last) aria-current="step" @endif>
                                <div class="linha-do-tempo__cabecalho">
                                    <x-status-da-solicitacao :status="$movimentacao->status_novo" />
                                    <time datetime="{{ $movimentacao->created_at->toIso8601String() }}" class="linha-do-tempo__data">
                                        {{ $movimentacao->created_at->format('d/m/Y \à\s H:i') }}
                                    </time>
                                    @if ($loop->last)
                                        <span class="linha-do-tempo__marca">Situação atual</span>
                                    @endif
                                </div>
                                @if ($movimentacao->mensagem)
                                    <p class="linha-do-tempo__mensagem">{{ $movimentacao->mensagem }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            <section class="minhas-solicitacoes-secao" aria-labelledby="titulo-dados">
                <h2 id="titulo-dados" class="minhas-solicitacoes-secao__titulo">Dados enviados</h2>

                <dl class="minhas-solicitacoes-dados">
                    @foreach ($detalhe->campos as $rotulo => $valor)
                        <div class="minhas-solicitacoes-dados__item">
                            <dt>{{ $rotulo }}</dt>
                            <dd>{{ $valor }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            @if ($detalhe->anexos !== [])
                <section class="minhas-solicitacoes-secao" aria-labelledby="titulo-anexos">
                    <h2 id="titulo-anexos" class="minhas-solicitacoes-secao__titulo">Anexos</h2>

                    <ul class="minhas-solicitacoes-anexos">
                        @foreach ($detalhe->anexos as $anexo)
                            <li>
                                <a href="{{ route('minhas-solicitacoes.anexos.baixar', [$detalhe->resumo->protocolo, $anexo['posicao']]) }}" class="minhas-solicitacoes-anexos__link">
                                    <i class="fa-solid fa-download" aria-hidden="true"></i>
                                    <span>Baixar {{ $anexo['nome'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

        </div>
    </div>
@endsection
