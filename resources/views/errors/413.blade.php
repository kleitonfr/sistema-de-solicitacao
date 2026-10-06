@extends('layouts.app')

@section('title', 'Arquivos grandes demais — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="categorias-servico-page">
        <div class="container">

            <div class="categorias-servico-cabecalho">
                <h1 class="categorias-servico-cabecalho__titulo">Os arquivos enviados são grandes demais</h1>
                <p class="categorias-servico-cabecalho__subtitulo">
                    O tamanho dos arquivos ultrapassou o limite permitido, e sua solicitação não foi enviada.
                    Volte ao formulário, escolha menos arquivos ou arquivos menores e envie novamente.
                </p>
            </div>

            <button type="button" class="cadastro-btn" onclick="history.back()">Voltar ao formulário</button>

        </div>
    </div>
@endsection
