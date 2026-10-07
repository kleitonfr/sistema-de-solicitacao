@extends('layouts.app')

@section('title', 'Manifestação Registrada — Ouvidoria — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="ouvidoria-page">
        <div class="container">

            <div class="confirmacao confirmacao--vermelho">
                <span class="confirmacao__icone">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                </span>

                <h1 class="confirmacao__titulo">Manifestação registrada com sucesso</h1>
                <p class="confirmacao__texto">
                    Sua manifestação foi recebida pela Ouvidoria. Guarde o número de protocolo
                    abaixo para acompanhar o andamento.
                </p>

                <div class="confirmacao__protocolo">
                    <span class="confirmacao__protocolo-rotulo">Número de protocolo</span>
                    <strong class="confirmacao__protocolo-valor">{{ $manifestacao->protocolo }}</strong>
                </div>

                <dl class="confirmacao__resumo">
                    <div class="confirmacao__resumo-item">
                        <dt>Tipo</dt>
                        <dd>{{ $manifestacao->rotuloDoTipo() }}</dd>
                    </div>
                    @if ($manifestacao->orgao_ou_setor)
                        <div class="confirmacao__resumo-item">
                            <dt>Órgão ou setor</dt>
                            <dd>{{ $manifestacao->orgao_ou_setor }}</dd>
                        </div>
                    @endif
                    @if ($manifestacao->anexos->isNotEmpty())
                        <div class="confirmacao__resumo-item">
                            <dt>Anexos</dt>
                            <dd>{{ $manifestacao->anexos->count() }} {{ $manifestacao->anexos->count() === 1 ? 'arquivo' : 'arquivos' }}</dd>
                        </div>
                    @endif
                </dl>

                <a href="{{ route('ouvidoria.index') }}" class="confirmacao__voltar">
                    Voltar para a Ouvidoria
                </a>
            </div>

        </div>
    </div>
@endsection
