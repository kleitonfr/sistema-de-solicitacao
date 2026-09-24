@extends('layouts.acesso')

@section('title', 'Cadastro — ESIC')

@section('classe_modo_esic', 'modo-esic')

@section('content-esic')
    @include('esic.parciais.formulario-cadastro')
@endsection
