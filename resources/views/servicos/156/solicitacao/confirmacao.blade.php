@extends('layouts.app')

@section('title', 'Solicitação registrada — Portal 156 — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="categorias-servico-page">
        <div class="container">

            <div class="confirmacao confirmacao--amarelo">
                <span class="confirmacao__icone">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                </span>

                <h1 class="confirmacao__titulo">Solicitação registrada com sucesso</h1>
                <p class="confirmacao__texto">
                    Sua solicitação foi recebida. Guarde o número de protocolo abaixo para consultas futuras.
                </p>

                <div class="confirmacao__protocolo">
                    <span class="confirmacao__protocolo-rotulo">Número de protocolo</span>
                    <strong class="confirmacao__protocolo-valor">{{ $solicitacao->protocolo }}</strong>
                </div>

                <dl class="confirmacao__resumo">
                    <div class="confirmacao__resumo-item">
                        <dt>Serviço</dt>
                        <dd>{{ $solicitacao->servico->nome }}</dd>
                    </div>
                    <div class="confirmacao__resumo-item">
                        <dt>Categoria</dt>
                        <dd>{{ $solicitacao->categoria->nome }}</dd>
                    </div>
                    <div class="confirmacao__resumo-item">
                        <dt>Local</dt>
                        <dd>{{ $solicitacao->enderecoCompleto() }}</dd>
                    </div>
                    <div class="confirmacao__resumo-item">
                        <dt>Telefone de contato</dt>
                        <dd>{{ $solicitacao->telefone_contato }}</dd>
                    </div>
                    @if ($solicitacao->anexos->isNotEmpty())
                        <div class="confirmacao__resumo-item">
                            <dt>Anexos</dt>
                            <dd>
                                {{ trans_choice(':total arquivo|:total arquivos', $solicitacao->anexos->count(), ['total' => $solicitacao->anexos->count()]) }}
                            </dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('servicos.156.categorias') }}" class="confirmacao__voltar">
                    Voltar para as categorias
                </a>
            </div>

        </div>
    </div>
@endsection
