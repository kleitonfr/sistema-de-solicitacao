@extends('layouts.app')

@section('title', 'Minhas solicitações — Prefeitura Municipal de Caraguatatuba')

@use('App\Services\MinhasSolicitacoesService')

@php
    $possuiFiltro = $chaveDoTipo !== null || $busca !== null;

    $parametrosDaBusca = $chaveDoTipo === null ? [] : ['servico' => $chaveDoTipo];
@endphp

@section('content')
    <div class="minhas-solicitacoes-page">
        <div class="container">

            <div class="minhas-solicitacoes-cabecalho">
                <h1 class="minhas-solicitacoes-cabecalho__titulo">Minhas solicitações</h1>
                <p class="minhas-solicitacoes-cabecalho__subtitulo">
                    Acompanhe o andamento do que você registrou no Portal 156, no e-SIC e na Ouvidoria.
                </p>
            </div>

            @if (session('erro_geral'))
                <x-cadastro-alerta tipo="erro">{{ session('erro_geral') }}</x-cadastro-alerta>
            @endif

            <form action="{{ route('minhas-solicitacoes.index') }}" method="GET" role="search" class="minhas-solicitacoes-busca">
                @if ($chaveDoTipo !== null)
                    <input type="hidden" name="servico" value="{{ $chaveDoTipo }}">
                @endif

                <label for="busca" class="minhas-solicitacoes-busca__rotulo">Buscar pelo número do protocolo</label>
                <div class="minhas-solicitacoes-busca__campo">
                    <input
                        type="search"
                        id="busca"
                        name="busca"
                        value="{{ $busca ?? old('busca') }}"
                        class="minhas-solicitacoes-busca__entrada @error('busca') is-invalid @enderror"
                        placeholder="Exemplo: OUV-2026-000001"
                        maxlength="20"
                        @error('busca') aria-describedby="busca-erro" @enderror
                    >
                    <button type="submit" class="minhas-solicitacoes-busca__botao">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        Buscar
                    </button>
                </div>
                @error('busca')
                    <span id="busca-erro" class="minhas-solicitacoes-busca__erro">{{ $message }}</span>
                @enderror
                @error('servico')
                    <span class="minhas-solicitacoes-busca__erro">{{ $message }}</span>
                @enderror
            </form>

            <nav class="minhas-solicitacoes-filtros" aria-label="Filtrar por serviço">
                <a
                    href="{{ route('minhas-solicitacoes.index', $busca === null ? [] : ['busca' => $busca]) }}"
                    class="minhas-solicitacoes-filtros__aba {{ $chaveDoTipo === null ? 'is-active' : '' }}"
                    @if ($chaveDoTipo === null) aria-current="page" @endif
                >Todas</a>
                @foreach (MinhasSolicitacoesService::TIPOS as $chave => $tipo)
                    <a
                        href="{{ route('minhas-solicitacoes.index', array_filter(['servico' => $chave, 'busca' => $busca])) }}"
                        class="minhas-solicitacoes-filtros__aba tipo-{{ $chave }} {{ $chaveDoTipo === $chave ? 'is-active' : '' }}"
                        @if ($chaveDoTipo === $chave) aria-current="page" @endif
                    >{{ $tipo['rotulo'] }}</a>
                @endforeach
            </nav>

            @if ($solicitacoes->isEmpty())
                <div class="minhas-solicitacoes-vazio">
                    <i class="fa-solid fa-folder-open minhas-solicitacoes-vazio__icone" aria-hidden="true"></i>
                    @if ($possuiFiltro)
                        <p class="minhas-solicitacoes-vazio__texto">Não encontramos solicitações com esses critérios.</p>
                        <a href="{{ route('minhas-solicitacoes.index') }}" class="minhas-solicitacoes-vazio__acao">Ver todas as solicitações</a>
                    @else
                        <p class="minhas-solicitacoes-vazio__texto">Você ainda não registrou nenhuma solicitação.</p>
                        <a href="{{ route('servicos.index') }}" class="minhas-solicitacoes-vazio__acao">Ver os serviços disponíveis</a>
                    @endif
                </div>
            @else
                <p class="minhas-solicitacoes-resultado" role="status">
                    {{ $solicitacoes->total() }} {{ $solicitacoes->total() === 1 ? 'solicitação encontrada' : 'solicitações encontradas' }}
                </p>

                <ul class="minhas-solicitacoes-lista">
                    @foreach ($solicitacoes as $resumo)
                        <li>
                            <a href="{{ route('minhas-solicitacoes.show', $resumo->protocolo) }}" class="minhas-solicitacao tipo-{{ $resumo->chaveDoTipo }}">
                                <div class="minhas-solicitacao__principal">
                                    <span class="minhas-solicitacao__tipo">{{ $resumo->rotuloDoTipo }}</span>
                                    <h2 class="minhas-solicitacao__titulo">{{ $resumo->titulo }}</h2>
                                    <p class="minhas-solicitacao__detalhes">
                                        Protocolo <strong>{{ $resumo->protocolo }}</strong>
                                        · Registrada em
                                        <time datetime="{{ $resumo->registradaEm->toDateString() }}">{{ $resumo->registradaEm->format('d/m/Y') }}</time>
                                    </p>
                                </div>
                                <div class="minhas-solicitacao__situacao">
                                    <x-status-da-solicitacao :status="$resumo->status" />
                                    <span class="minhas-solicitacao__ver">
                                        Ver andamento
                                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

                @if ($solicitacoes->hasPages())
                    <nav class="minhas-solicitacoes-paginacao" aria-label="Paginação">
                        @if ($solicitacoes->previousPageUrl())
                            <a href="{{ $solicitacoes->previousPageUrl() }}" rel="prev" class="minhas-solicitacoes-paginacao__link">
                                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Anterior
                            </a>
                        @endif
                        <span class="minhas-solicitacoes-paginacao__pagina">
                            Página {{ $solicitacoes->currentPage() }} de {{ $solicitacoes->lastPage() }}
                        </span>
                        @if ($solicitacoes->nextPageUrl())
                            <a href="{{ $solicitacoes->nextPageUrl() }}" rel="next" class="minhas-solicitacoes-paginacao__link">
                                Próxima <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        @endif
                    </nav>
                @endif
            @endif

        </div>
    </div>
@endsection
