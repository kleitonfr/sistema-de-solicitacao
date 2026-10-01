@extends('layouts.app')

@section('title', 'Pedido Registrado — e-SIC — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="esic-page">
        <div class="container">

            <div class="esic-confirmacao">
                <span class="esic-confirmacao__icone">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                </span>

                <h1 class="esic-confirmacao__titulo">Pedido registrado com sucesso</h1>
                <p class="esic-confirmacao__texto">
                    Sua solicitação foi recebida. Guarde o número de protocolo abaixo para
                    acompanhar o andamento do seu pedido.
                </p>

                <div class="esic-confirmacao__protocolo">
                    <span class="esic-confirmacao__protocolo-rotulo">Número de protocolo</span>
                    <strong class="esic-confirmacao__protocolo-valor">{{ $solicitacao->protocolo }}</strong>
                </div>

                <dl class="esic-confirmacao__resumo">
                    <div class="esic-confirmacao__resumo-item">
                        <dt>Assunto</dt>
                        <dd>{{ $solicitacao->rotuloDoAssunto() }}</dd>
                    </div>
                    @if ($solicitacao->anexo_nome_original)
                        <div class="esic-confirmacao__resumo-item">
                            <dt>Anexo</dt>
                            <dd>{{ $solicitacao->anexo_nome_original }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('esic.index') }}" class="esic-acao__botao esic-confirmacao__voltar">
                    Voltar para o e-SIC
                </a>
            </div>

        </div>
    </div>
@endsection
