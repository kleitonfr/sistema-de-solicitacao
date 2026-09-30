@extends('layouts.app')

@section('title', 'Categorias de serviço — Portal 156 — Prefeitura Municipal de Caraguatatuba')

@use('App\Http\Requests\BuscarCategoriasServicoRequest')

@php
    $filtros = [
        BuscarCategoriasServicoRequest::FILTRO_TODAS => 'Todas as categorias',
        BuscarCategoriasServicoRequest::FILTRO_PRINCIPAIS => 'Principais serviços',
    ];
@endphp

@section('content')
    <div class="categorias-servico-page">
        <div class="container">

            <a href="{{ route('servicos.index') }}" class="categorias-servico-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para a escolha de serviço
            </a>

            <div class="categorias-servico-cabecalho">
                <h1 class="categorias-servico-cabecalho__titulo">Portal 156</h1>
                <p class="categorias-servico-cabecalho__subtitulo">
                    Escolha a categoria que corresponde à sua solicitação.
                </p>
            </div>

            <form method="GET" action="{{ route('servicos.156.categorias') }}" role="search"
                class="categorias-servico-busca">
                <label for="busca" class="categorias-servico-busca__rotulo">Buscar categoria</label>
                <div class="categorias-servico-busca__campo">
                    <input type="search" id="busca" name="busca" maxlength="100"
                        value="{{ $termoDeBusca }}"
                        placeholder="Ex.: iluminação, árvore, coleta"
                        class="categorias-servico-busca__entrada @error('busca') is-invalid @enderror"
                        @error('busca') aria-invalid="true" aria-describedby="busca-erro" @enderror>
                    <input type="hidden" name="filtro" value="{{ $filtroSelecionado }}">
                    <button type="submit" class="categorias-servico-busca__botao">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        Buscar
                    </button>
                </div>
                @error('busca')
                    <span id="busca-erro" class="categorias-servico-busca__erro" role="alert">{{ $message }}</span>
                @enderror
                @error('filtro')
                    <span class="categorias-servico-busca__erro" role="alert">{{ $message }}</span>
                @enderror
            </form>

            <nav class="categorias-servico-filtros" aria-label="Filtrar categorias">
                @foreach ($filtros as $filtro => $rotuloDoFiltro)
                    <a href="{{ route('servicos.156.categorias', array_filter(['filtro' => $filtro, 'busca' => $termoDeBusca])) }}"
                        class="categorias-servico-filtros__aba {{ $filtroSelecionado === $filtro ? 'is-active' : '' }}"
                        @if ($filtroSelecionado === $filtro) aria-current="page" @endif>
                        {{ $rotuloDoFiltro }}
                    </a>
                @endforeach
            </nav>
            <p class="categorias-servico-resultado" role="status">
                @if ($termoDeBusca !== null)
                    {{ trans_choice(':total categoria encontrada|:total categorias encontradas', $categorias->count(), ['total' => $categorias->count()]) }}
                    para “{{ $termoDeBusca }}”.
                @else
                    {{ trans_choice(':total categoria|:total categorias', $categorias->count(), ['total' => $categorias->count()]) }}
                @endif
            </p>
            
            <div class="container-categorias">
                @if ($categorias->isEmpty())
                    <div class="categorias-servico-vazio">
                        <i class="fa-regular fa-folder-open categorias-servico-vazio__icone" aria-hidden="true"></i>
                        <p class="categorias-servico-vazio__texto">
                            @if ($termoDeBusca !== null)
                                Não encontramos categorias com esse termo. Tente outra palavra.
                            @else
                                Nenhuma categoria disponível no momento.
                            @endif
                        </p>
                        @if ($termoDeBusca !== null)
                            <a href="{{ route('servicos.156.categorias', ['filtro' => $filtroSelecionado]) }}"
                                class="categorias-servico-vazio__acao">
                                Limpar busca
                            </a>
                        @endif
                    </div>
                @else
                    {{-- Pendente: cada card leva para "#" porque a tela de serviços da
                         categoria ainda não existe. --}}
                    <div class="categorias-servico-grade">
                        @foreach ($categorias as $categoria)
                            <a href="#" class="categoria-servico">
                                <span class="categoria-servico__icone">
                                    <i class="{{ $categoria->icone() }}" aria-hidden="true"></i>
                                </span>
                                <h2 class="categoria-servico__nome">{{ $categoria->nome }}</h2>
                                <p class="categoria-servico__descricao">{{ $categoria->descricao }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
