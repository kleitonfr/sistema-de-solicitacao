@extends('layouts.acesso')

@section('title', 'Entrar — Sistema 651')

@section('content')
    <div class="acesso-cabecalho-formulario">
        <h1 class="acesso-cabecalho-formulario__titulo">Bem-vindo de volta</h1>
        <p class="acesso-cabecalho-formulario__subtitulo">Acesse sua conta para continuar usando os serviços digitais do município.</p>
    </div>

    <div class="cadastro-card">

        @if ($errors->any())
            <div class="cadastro-alert cadastro-alert--erro" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('acesso.store') }}" method="POST" novalidate>
            @csrf

            <div class="cadastro-field cadastro-field--span-full">
                <label for="email" class="cadastro-label">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="cadastro-input @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                >
                @error('email')
                    <span class="cadastro-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="cadastro-field cadastro-field--span-full mt-4">
                <label for="senha" class="cadastro-label">Senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    class="cadastro-input @error('senha') is-invalid @enderror"
                    required
                    autocomplete="current-password"
                >
                @error('senha')
                    <span class="cadastro-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="acesso-links-formulario">
                <a href="#" class="acesso-links-formulario__link">Esqueci minha senha</a>
            </div>

            <div class="cadastro-actions">
                <button type="submit" class="cadastro-btn">Entrar</button>
            </div>
        </form>

    </div>
@endsection
