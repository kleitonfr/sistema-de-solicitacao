@extends('layouts.acesso')

@section('title', 'Entrar — ESIC')

@section('classe_modo_esic', 'modo-esic')

@section('content')
    @include('portal.parciais.formulario-entrar')
@endsection

@section('content-esic')
    @include('esic.parciais.formulario-entrar')
@endsection