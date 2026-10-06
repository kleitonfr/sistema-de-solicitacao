@extends('layouts.app')

@section('title', 'Registrar Pedido — e-SIC — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="esic-page">
        <div class="container">

            <a href="{{ route('esic.index') }}" class="esic-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para o e-SIC
            </a>

            <div class="esic-faixa">
                <h1 class="esic-faixa__titulo">Registrar Novo Pedido</h1>
                <p class="esic-faixa__subtitulo">
                    Preencha as informações abaixo para solicitar acesso a uma informação pública.
                    Você receberá um número de protocolo para acompanhar sua solicitação.
                </p>
            </div>

            <div class="cadastro-card cadastro-card--solicitacao">

                @if ($errors->any())
                    <x-cadastro-alerta tipo="erro">Verifique os campos destacados abaixo e tente novamente.</x-cadastro-alerta>
                @endif

                <form action="{{ route('esic.solicitacao.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <section class="cadastro-card__secao">
                        <div class="cadastro-card__cabecalho-secao">
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--verde">
                                <i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Dados do Pedido</h2>
                        </div>

                        <div class="cadastro-grid">
                            <div class="cadastro-field cadastro-field--span-full">
                                <label for="assunto" class="cadastro-label">Assunto</label>
                                <select id="assunto" name="assunto" class="cadastro-select @error('assunto') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('assunto') ? '' : 'selected' }}></option>
                                    @foreach (\App\Models\SolicitacaoEsic::ASSUNTOS as $valor => $rotulo)
                                        <option value="{{ $valor }}" @selected(old('assunto') === $valor)>{{ $rotulo }}</option>
                                    @endforeach
                                </select>
                                @error('assunto')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field cadastro-field--span-full">
                                <label for="descricao" class="cadastro-label cadastro-label-fixo">Descrição detalhada</label>
                                <p class="cadastro-texto-auxiliar cadastro-texto-auxiliar--sob-rotulo">
                                    Descreva de forma clara e objetiva a informação que você deseja obter.
                                </p>
                                <textarea
                                    id="descricao"
                                    name="descricao"
                                    class="cadastro-input cadastro-textarea @error('descricao') is-invalid @enderror"
                                    placeholder=" "
                                    rows="6"
                                    required
                                >{{ old('descricao') }}</textarea>
                                @error('descricao')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="cadastro-card__secao">
                        <div class="cadastro-card__cabecalho-secao">
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--verde">
                                <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Arquivo anexo (opcional)</h2>
                        </div>

                        <x-dropzone-anexos
                            nome="anexo"
                            rotulo="Anexar arquivo"
                            aceitos=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            ajuda="PDF, JPG, PNG, DOC ou DOCX — até 10 MB"
                            :invalido="$errors->has('anexo')"
                        />
                        @error('anexo')
                            <span class="cadastro-error">{{ $message }}</span>
                        @enderror
                    </section>

                    <div class="cadastro-actions">
                        <button type="submit" class="cadastro-btn">Enviar Pedido</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection
