@extends('layouts.app')

@section('title', 'Escolha um serviço — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="servicos-page">
        <div class="container">

            <div class="servicos-cabecalho">
                <h1 class="servicos-cabecalho__titulo">O que você deseja acessar?</h1>
                <p class="servicos-cabecalho__subtitulo">Escolha um dos serviços digitais da Prefeitura de Caraguatatuba.</p>
            </div>

            {{--
                Nenhum dos 3 destinos existe como rota/sistema ainda — os
                links abaixo apontam para "#" propositalmente (ver
                ServicoController). Quando cada serviço tiver seu próprio
                sistema/rota, trocar o href correspondente.
            --}}
            <div class="servicos-grid">

                <a href="#" class="servicos-card servicos-card--vermelho">
                    <span class="servicos-card__icone">
                        <i class="fa-solid fa-headset" aria-hidden="true"></i>
                    </span>
                    <h2 class="servicos-card__titulo">Ouvidoria</h2>
                    <p class="servicos-card__descricao">Registre elogios, sugestões, reclamações e denúncias.</p>
                </a>

                <a href="{{ route('cadastro.index') }}" class="servicos-card servicos-card--amarelo">
                    <span class="servicos-card__icone">
                        <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                    </span>
                    <h2 class="servicos-card__titulo">Portal 156</h2>
                    <p class="servicos-card__descricao">Solicite serviços públicos e acompanhe suas solicitações.</p>
                </a>

                <a href="#" class="servicos-card servicos-card--verde">
                    <span class="servicos-card__icone">
                        <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                    </span>
                    <h2 class="servicos-card__titulo">e-SIC</h2>
                    <p class="servicos-card__descricao">Solicite acesso a informações públicas do município.</p>
                </a>

            </div>

        </div>
    </div>
@endsection
