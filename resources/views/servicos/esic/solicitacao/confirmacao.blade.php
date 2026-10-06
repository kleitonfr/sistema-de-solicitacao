@extends('layouts.app')

@section('title', 'Pedido Registrado — e-SIC — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="esic-page">
        <div class="container">

            <div class="confirmacao confirmacao--verde">
                <span class="confirmacao__icone">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                </span>

                <h1 class="confirmacao__titulo">Pedido registrado com sucesso</h1>
                <p class="confirmacao__texto">
                    Sua solicitação foi recebida. Guarde o número de protocolo abaixo para
                    acompanhar o andamento do seu pedido.
                </p>

                <div class="confirmacao__protocolo">
                    <span class="confirmacao__protocolo-rotulo">Número de protocolo</span>
                    <strong class="confirmacao__protocolo-valor">{{ $solicitacao->protocolo }}</strong>
                </div>

                <dl class="confirmacao__resumo">
                    <div class="confirmacao__resumo-item">
                        <dt>Assunto</dt>
                        <dd>{{ $solicitacao->rotuloDoAssunto() }}</dd>
                    </div>
                    @if ($solicitacao->anexo_nome_original)
                        <div class="confirmacao__resumo-item">
                            <dt>Anexo</dt>
                            <dd>{{ $solicitacao->anexo_nome_original }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('esic.index') }}" class="confirmacao__voltar">
                    Voltar para o e-SIC
                </a>
            </div>

        </div>
    </div>
@endsection
