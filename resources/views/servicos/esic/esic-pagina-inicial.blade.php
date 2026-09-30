@extends('layouts.app')

@section('title', 'e-SIC — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="esic-page">
        <div class="container">

            <a href="{{ route('servicos.index') }}" class="esic-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para a escolha de serviço
            </a>

            <div class="esic-faixa">
                <h1 class="esic-faixa__titulo">e-SIC</h1>
                <p class="esic-faixa__subtitulo">
                    O e-SIC permite que qualquer pessoa, física ou jurídica, encaminhe pedidos de
                    acesso à informação e receba a resposta da solicitação realizada à Prefeitura
                    de Caraguatatuba, em conformidade com a Lei de Acesso à Informação (LAI).
                </p>
            </div>

            {{-- Pendente: os 3 cards levam para "#" — os formulários de pedido, consulta
                 e recurso ainda não existem (ver EsicController). --}}
            <div class="esic-acoes">
                <div class="esic-acao">
                    <span class="esic-acao__icone">
                        <i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i>
                    </span>
                    <h2 class="esic-acao__titulo">Registrar Novo Pedido</h2>
                    <p class="esic-acao__descricao">
                        Inicie uma nova solicitação. O processo é rápido e você receberá um número
                        de protocolo para acompanhamento.
                    </p>
                    <a href="#" class="esic-acao__botao">Iniciar Registro</a>
                </div>

                <div class="esic-acao">
                    <span class="esic-acao__icone">
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    </span>
                    <h2 class="esic-acao__titulo">Consultar Pedido</h2>
                    <p class="esic-acao__descricao">
                        Acompanhe o andamento de uma solicitação com o número do seu protocolo.
                    </p>
                    <a href="#" class="esic-acao__botao">Consultar Agora</a>
                </div>

                <div class="esic-acao">
                    <span class="esic-acao__icone">
                        <i class="fa-solid fa-gavel" aria-hidden="true"></i>
                    </span>
                    <h2 class="esic-acao__titulo">Interpor Recurso</h2>
                    <p class="esic-acao__descricao">
                        Não está satisfeito com a resposta recebida? Entre com um recurso da sua
                        solicitação.
                    </p>
                    <a href="#" class="esic-acao__botao">Iniciar Recurso</a>
                </div>
            </div>

            <div class="esic-info-grid">
                <div class="esic-info-card">
                    <h2 class="esic-info-card__titulo">Recursos</h2>
                    <p class="esic-info-card__texto">
                        Caso o acesso à informação seja negado ou a resposta seja insatisfatória,
                        você pode entrar com um recurso da sua solicitação.
                    </p>
                </div>

                <div class="esic-info-card">
                    <h2 class="esic-info-card__titulo">Lei de Acesso à Informação</h2>
                    <p class="esic-info-card__texto">
                        O e-SIC segue a Lei Federal nº 12.527/2011 (Lei de Acesso à Informação),
                        que regulamenta o direito de acesso a informações públicas.
                    </p>
                    <a href="https://www.planalto.gov.br/ccivil_03/_ato2011-2014/2011/lei/l12527.htm"
                        target="_blank" rel="noopener" class="esic-info-card__link">
                        Conheça a LAI
                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="esic-dicas">
                <h2 class="esic-dicas__titulo">Dicas para um bom pedido</h2>
                <ol class="esic-dicas__lista">
                    <li>Seja específico. Evite pedidos genéricos como "quero saber sobre as contas".</li>
                    <li>Indique o período (datas) de interesse para a informação solicitada.</li>
                    <li>Verifique se a informação já não está disponível no Portal da Transparência.</li>
                </ol>
            </div>

        </div>
    </div>
@endsection
