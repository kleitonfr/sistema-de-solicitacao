@extends('layouts.app')

@section('title', $configuracaoDoTipo['rotulo'].' — Ouvidoria — Prefeitura Municipal de Caraguatatuba')

@use('App\Http\Requests\RegistrarManifestacaoRequest')
@use('App\Models\ManifestacaoOuvidoria')

@php
    $errosDeAnexos = collect($errors->get('anexos'))->merge($errors->get('anexos.*'))->flatten()->unique();

    $extensoesDoInput = '.'.str_replace(',', ',.', RegistrarManifestacaoRequest::EXTENSOES_ACEITAS);

    $ajudaDosAnexos = 'Até '.RegistrarManifestacaoRequest::MAXIMO_DE_ANEXOS.' arquivos de até '
        .RegistrarManifestacaoRequest::TAMANHO_MAXIMO_POR_ARQUIVO_EM_MB.' MB cada, somando no máximo '
        .RegistrarManifestacaoRequest::TAMANHO_MAXIMO_TOTAL_EM_MB.' MB (JPG, PNG, PDF, MP4, MOV ou WEBM)';
@endphp

@section('content')
    <div class="ouvidoria-page">
        <div class="container">

            <a href="{{ route('ouvidoria.index') }}" class="ouvidoria-voltar">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Voltar para a Ouvidoria
            </a>

            <div class="ouvidoria-faixa">
                <h1 class="ouvidoria-faixa__titulo">{{ $configuracaoDoTipo['rotulo'] }}</h1>
                <p class="ouvidoria-faixa__subtitulo">{{ $configuracaoDoTipo['orientacao'] }}</p>
            </div>

            <div class="cadastro-card cadastro-card--solicitacao">

                @if (session('erro_geral'))
                    <x-cadastro-alerta tipo="erro">{{ session('erro_geral') }}</x-cadastro-alerta>
                @endif

                @if ($errors->any())
                    <x-cadastro-alerta tipo="erro">Verifique os campos destacados abaixo e tente novamente.</x-cadastro-alerta>
                @endif

                <form action="{{ route('ouvidoria.manifestacao.registrar', $tipo) }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <section class="cadastro-card__secao">
                        <div class="cadastro-card__cabecalho-secao">
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--vermelho">
                                <i class="fa-solid fa-comment-dots" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Sua manifestação</h2>
                        </div>

                        <p class="cadastro-texto-auxiliar">
                            Os campos marcados como opcionais podem ficar em branco.
                        </p>

                        <div class="cadastro-grid">
                            <div class="cadastro-field cadastro-field--span-full">
                                <label for="orgao_ou_setor" class="cadastro-label">Órgão ou setor (opcional)</label>
                                <input
                                    type="text"
                                    id="orgao_ou_setor"
                                    name="orgao_ou_setor"
                                    class="cadastro-input @error('orgao_ou_setor') is-invalid @enderror"
                                    value="{{ old('orgao_ou_setor') }}"
                                    placeholder=" "
                                    maxlength="255"
                                >
                                @error('orgao_ou_setor')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (ManifestacaoOuvidoria::tipoTemCampo($tipo, 'data_do_fato'))
                                <div class="cadastro-field">
                                    <label for="data_do_fato" class="cadastro-label">Data do fato (opcional)</label>
                                    <input
                                        type="date"
                                        id="data_do_fato"
                                        name="data_do_fato"
                                        class="cadastro-input @error('data_do_fato') is-invalid @enderror"
                                        value="{{ old('data_do_fato') }}"
                                        max="{{ now()->format('Y-m-d') }}"
                                    >
                                    @error('data_do_fato')
                                        <span class="cadastro-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if (ManifestacaoOuvidoria::tipoTemCampo($tipo, 'local_do_fato'))
                                <div class="cadastro-field">
                                    <label for="local_do_fato" class="cadastro-label">Local do fato (opcional)</label>
                                    <input
                                        type="text"
                                        id="local_do_fato"
                                        name="local_do_fato"
                                        class="cadastro-input @error('local_do_fato') is-invalid @enderror"
                                        value="{{ old('local_do_fato') }}"
                                        placeholder=" "
                                        maxlength="255"
                                    >
                                    @error('local_do_fato')
                                        <span class="cadastro-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if (ManifestacaoOuvidoria::tipoTemCampo($tipo, 'servidor_ou_setor'))
                                <div class="cadastro-field cadastro-field--span-full">
                                    <label for="servidor_ou_setor" class="cadastro-label">{{ $configuracaoDoTipo['rotulo_do_servidor_ou_setor'] }} (opcional)</label>
                                    <input
                                        type="text"
                                        id="servidor_ou_setor"
                                        name="servidor_ou_setor"
                                        class="cadastro-input @error('servidor_ou_setor') is-invalid @enderror"
                                        value="{{ old('servidor_ou_setor') }}"
                                        placeholder=" "
                                        maxlength="255"
                                    >
                                    @error('servidor_ou_setor')
                                        <span class="cadastro-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div class="cadastro-field cadastro-field--span-full">
                                <label for="descricao" class="cadastro-label cadastro-label-fixo">{{ $configuracaoDoTipo['rotulo_da_descricao'] }}</label>
                                <p class="cadastro-texto-auxiliar cadastro-texto-auxiliar--sob-rotulo">
                                    Escreva de forma clara e objetiva. Evite incluir dados pessoais de terceiros que não sejam necessários.
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
                            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--vermelho">
                                <i class="fa-solid fa-paperclip" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__titulo-secao">Arquivos anexos (opcional)</h2>
                        </div>

                        <x-dropzone-anexos
                            nome="anexos"
                            rotulo="Anexar arquivos"
                            :aceitos="$extensoesDoInput"
                            :ajuda="$ajudaDosAnexos"
                            cor="vermelho"
                            :multiplo="true"
                            :maximo-de-arquivos="RegistrarManifestacaoRequest::MAXIMO_DE_ANEXOS"
                            :invalido="$errosDeAnexos->isNotEmpty()"
                        />
                        @foreach ($errosDeAnexos as $erroDeAnexo)
                            <span class="cadastro-error">{{ $erroDeAnexo }}</span>
                        @endforeach
                    </section>

                    <div class="cadastro-actions">
                        <button type="submit" class="cadastro-btn">Enviar {{ $configuracaoDoTipo['rotulo'] }}</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
@endsection
