@extends('layouts.app')

@section('title', $servico->nome.' — Portal 156 — Prefeitura Municipal de Caraguatatuba')

@use('App\Http\Requests\RegistrarSolicitacao156Request')
@use('App\Models\Solicitacao156')

@php
    $errosDeAnexos = collect($errors->get('anexos'))->merge($errors->get('anexos.*'))->flatten()->unique();

    $extensoesDoInput = '.'.str_replace(',', ',.', RegistrarSolicitacao156Request::EXTENSOES_ACEITAS);

    $ajudaDosAnexos = 'Até '.RegistrarSolicitacao156Request::MAXIMO_DE_ANEXOS.' arquivos: fotos de até '
        .RegistrarSolicitacao156Request::TAMANHO_MAXIMO_DA_FOTO_EM_MB.' MB e vídeos de até '
        .RegistrarSolicitacao156Request::TAMANHO_MAXIMO_DO_VIDEO_EM_MB.' MB (JPG, PNG, MP4, MOV ou WEBM)';
@endphp

@section('content')
    <div class="categorias-servico-page">
        <div class="container">

            <a href="{{ route('servicos.156.servicos', $servico->categoria->slug) }}" class="categorias-servico-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para {{ $servico->categoria->nome }}
            </a>

            <div class="categorias-servico-cabecalho">
                <h1 class="categorias-servico-cabecalho__titulo">{{ $servico->nome }}</h1>
                <p class="categorias-servico-cabecalho__subtitulo">{{ $servico->descricao }}</p>
            </div>

            <div class="cadastro-card cadastro-card--solicitacao">

                @if ($errors->any())
                    <x-cadastro-alerta tipo="erro">Verifique os campos destacados abaixo e tente novamente.</x-cadastro-alerta>
                @endif

                <form action="{{ route('servicos.156.solicitacao.registrar', $servico->slug) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <section class="cadastro-card__secao">
                        <div class="cadastro-card__cabecalho-secao">
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--amarelo">
                                <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Sua solicitação</h2>
                        </div>

                        <div class="cadastro-grid">
                            <div class="cadastro-field cadastro-field--span-full">
                                <label for="descricao" class="cadastro-label cadastro-label-fixo">Descreva a situação</label>
                                <p class="cadastro-texto-auxiliar cadastro-texto-auxiliar--sob-rotulo">
                                    Conte o que está acontecendo, de forma clara e objetiva.
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

                            <div class="cadastro-field">
                                <label for="telefone_contato" class="cadastro-label">Telefone de contato</label>
                                <input
                                    type="text"
                                    id="telefone_contato"
                                    name="telefone_contato"
                                    class="cadastro-input @error('telefone_contato') is-invalid @enderror"
                                    value="{{ old('telefone_contato') }}"
                                    placeholder=" "
                                    inputmode="tel"
                                    maxlength="15"
                                    data-mask="telefone-completo"
                                    autocomplete="tel"
                                    required
                                >
                                @error('telefone_contato')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="cadastro-card__secao">
                        <div class="cadastro-card__cabecalho-secao">
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--amarelo">
                                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Localização</h2>
                        </div>

                        <p class="cadastro-texto-auxiliar">
                            Cidade: <strong>{{ Solicitacao156::CIDADE_E_UF }}</strong>
                        </p>

                        <div class="cadastro-grid cadastro-grid--endereco">
                            <div class="cadastro-field">
                                <label for="cep" class="cadastro-label">CEP</label>
                                <input
                                    type="text"
                                    id="cep"
                                    name="cep"
                                    class="cadastro-input @error('cep') is-invalid @enderror"
                                    value="{{ old('cep') }}"
                                    placeholder=" "
                                    inputmode="numeric"
                                    maxlength="9"
                                    data-mask="cep"
                                    autocomplete="postal-code"
                                    required
                                >
                                @error('cep')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="logradouro" class="cadastro-label">Logradouro</label>
                                <input
                                    type="text"
                                    id="logradouro"
                                    name="logradouro"
                                    class="cadastro-input @error('logradouro') is-invalid @enderror"
                                    value="{{ old('logradouro') }}"
                                    placeholder=" "
                                    autocomplete="address-line1"
                                    required
                                >
                                @error('logradouro')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="bairro" class="cadastro-label">Bairro</label>
                                <input
                                    type="text"
                                    id="bairro"
                                    name="bairro"
                                    class="cadastro-input @error('bairro') is-invalid @enderror"
                                    value="{{ old('bairro') }}"
                                    placeholder=" "
                                    required
                                >
                                @error('bairro')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="numero" class="cadastro-label">Número</label>
                                <input
                                    type="text"
                                    id="numero"
                                    name="numero"
                                    class="cadastro-input @error('numero') is-invalid @enderror"
                                    value="{{ old('numero') }}"
                                    placeholder=" "
                                    maxlength="20"
                                    required
                                >
                                @error('numero')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="ponto_referencia" class="cadastro-label">Ponto de referência (opcional)</label>
                                <input
                                    type="text"
                                    id="ponto_referencia"
                                    name="ponto_referencia"
                                    class="cadastro-input @error('ponto_referencia') is-invalid @enderror"
                                    value="{{ old('ponto_referencia') }}"
                                    placeholder=" "
                                >
                                @error('ponto_referencia')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <section class="cadastro-card__secao">
                        <div class="cadastro-card__cabecalho-secao">
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--amarelo">
                                <i class="fa-solid fa-camera" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Fotos ou vídeos (opcional)</h2>
                        </div>

                        <x-dropzone-anexos
                            nome="anexos"
                            rotulo="Anexar fotos ou vídeos"
                            :aceitos="$extensoesDoInput"
                            :ajuda="$ajudaDosAnexos"
                            cor="amarelo"
                            :multiplo="true"
                            :maximo-de-arquivos="RegistrarSolicitacao156Request::MAXIMO_DE_ANEXOS"
                            :invalido="$errosDeAnexos->isNotEmpty()"
                        />
                        @foreach ($errosDeAnexos as $erroDeAnexo)
                            <span class="cadastro-error">{{ $erroDeAnexo }}</span>
                        @endforeach
                    </section>

                    <div class="cadastro-actions">
                        <button type="submit" class="cadastro-btn">Enviar Solicitação</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection
