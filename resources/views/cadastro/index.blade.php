@extends('layouts.app')

@section('title', 'Cadastro — Prefeitura Municipal de Caraguatatuba')

@section('content')
    <div class="cadastro-page">
        <div class="container">

            <div class="cadastro-heading">
                <h1 class="cadastro-heading__title">Cadastro de Cidadão</h1>
                <p class="cadastro-heading__subtitle">Complete as informações abaixo para acessar os serviços digitais do município.</p>
            </div>

            <div class="cadastro-card">

                {{-- Alertas de retorno da submissão (simulados até a API existir) --}}
                @if (session('status') === 'success')
                    <div class="cadastro-alert cadastro-alert--success" role="status">
                        {{ session('message') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="cadastro-alert cadastro-alert--error" role="alert">
                        Verifique os campos destacados abaixo e tente novamente.
                    </div>
                @endif

                <form action="{{ route('cadastro.store') }}" method="POST" novalidate>
                    @csrf

                    {{-- ==================== DADOS DO SOLICITANTE ==================== --}}
                    <section class="cadastro-card__section">
                        <div class="cadastro-card__section-header">
                            <span class="cadastro-card__section-icon cadastro-card__section-icon--red">
                                <i class="fa-solid fa-user" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__section-title">Dados do Solicitante</h2>
                        </div>

                        <div class="cadastro-grid">
                            <div class="cadastro-field">
                                <label for="nome_completo" class="cadastro-label">Nome Completo</label>
                                <input
                                    type="text"
                                    id="nome_completo"
                                    name="nome_completo"
                                    class="cadastro-input @error('nome_completo') is-invalid @enderror"
                                    value="{{ old('nome_completo') }}"
                                    required
                                    autocomplete="name"
                                >
                                @error('nome_completo')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="nome_social" class="cadastro-label">Nome Social (opcional)</label>
                                <input
                                    type="text"
                                    id="nome_social"
                                    name="nome_social"
                                    class="cadastro-input @error('nome_social') is-invalid @enderror"
                                    value="{{ old('nome_social') }}"
                                >
                                @error('nome_social')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="email" class="cadastro-label">E-mail</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="cadastro-input @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="email"
                                >
                                @error('email')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="cpf" class="cadastro-label">CPF</label>
                                <input
                                    type="text"
                                    id="cpf"
                                    name="cpf"
                                    class="cadastro-input @error('cpf') is-invalid @enderror"
                                    value="{{ old('cpf') }}"
                                    placeholder="000.000.000-00"
                                    inputmode="numeric"
                                    maxlength="14"
                                    data-mask="cpf"
                                    required
                                >
                                @error('cpf')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="nome_mae" class="cadastro-label">Nome Completo da Mãe (opcional)</label>
                                <input
                                    type="text"
                                    id="nome_mae"
                                    name="nome_mae"
                                    class="cadastro-input @error('nome_mae') is-invalid @enderror"
                                    value="{{ old('nome_mae') }}"
                                >
                                @error('nome_mae')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field-inline">
                                <div class="cadastro-field">
                                    <label for="data_nascimento" class="cadastro-label">Data de Nascimento</label>
                                    <input
                                        type="date"
                                        id="data_nascimento"
                                        name="data_nascimento"
                                        class="cadastro-input @error('data_nascimento') is-invalid @enderror"
                                        value="{{ old('data_nascimento') }}"
                                        required
                                    >
                                    @error('data_nascimento')
                                        <span class="cadastro-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="cadastro-field">
                                    <label for="sexo" class="cadastro-label">Sexo</label>
                                    <select
                                        id="sexo"
                                        name="sexo"
                                        class="cadastro-select @error('sexo') is-invalid @enderror"
                                        required
                                    >
                                        <option value="" disabled {{ old('sexo') ? '' : 'selected' }}>Selecione</option>
                                        <option value="feminino" @selected(old('sexo') === 'feminino')>Feminino</option>
                                        <option value="masculino" @selected(old('sexo') === 'masculino')>Masculino</option>
                                        <option value="outro" @selected(old('sexo') === 'outro')>Outro</option>
                                        <option value="nao_informar" @selected(old('sexo') === 'nao_informar')>Prefiro não informar</option>
                                    </select>
                                    @error('sexo')
                                        <span class="cadastro-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ==================== TELEFONE ==================== --}}
                    <section class="cadastro-card__section">
                        <div class="cadastro-card__section-header">
                            <span class="cadastro-card__section-icon cadastro-card__section-icon--green">
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__section-title">Telefone</h2>
                        </div>

                        <div class="cadastro-grid cadastro-grid--telefone">
                            <div class="cadastro-field">
                                <label for="telefone_tipo" class="cadastro-label">Tipo</label>
                                <select
                                    id="telefone_tipo"
                                    name="telefone_tipo"
                                    class="cadastro-select @error('telefone_tipo') is-invalid @enderror"
                                    required
                                >
                                    <option value="" disabled {{ old('telefone_tipo') ? '' : 'selected' }}>Selecione</option>
                                    <option value="celular" @selected(old('telefone_tipo') === 'celular')>Celular</option>
                                    <option value="residencial" @selected(old('telefone_tipo') === 'residencial')>Residencial</option>
                                    <option value="comercial" @selected(old('telefone_tipo') === 'comercial')>Comercial</option>
                                    <option value="recado" @selected(old('telefone_tipo') === 'recado')>Recado</option>
                                </select>
                                @error('telefone_tipo')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="telefone_ddd" class="cadastro-label">DDD</label>
                                <input
                                    type="text"
                                    id="telefone_ddd"
                                    name="telefone_ddd"
                                    class="cadastro-input @error('telefone_ddd') is-invalid @enderror"
                                    value="{{ old('telefone_ddd') }}"
                                    placeholder="ddd"
                                    inputmode="numeric"
                                    maxlength="3"
                                    required
                                >
                                @error('telefone_ddd')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="telefone_numero" class="cadastro-label">Telefone</label>
                                <input
                                    type="text"
                                    id="telefone_numero"
                                    name="telefone_numero"
                                    class="cadastro-input @error('telefone_numero') is-invalid @enderror"
                                    value="{{ old('telefone_numero') }}"
                                    placeholder="Telefone"
                                    inputmode="numeric"
                                    maxlength="20"
                                    data-mask="telefone"
                                    required
                                >
                                @error('telefone_numero')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="telefone_observacao" class="cadastro-label">Observação (opcional)</label>
                                <input
                                    type="text"
                                    id="telefone_observacao"
                                    name="telefone_observacao"
                                    class="cadastro-input @error('telefone_observacao') is-invalid @enderror"
                                    value="{{ old('telefone_observacao') }}"
                                >
                                @error('telefone_observacao')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- ==================== ENDEREÇO ==================== --}}
                    <section class="cadastro-card__section">
                        <div class="cadastro-card__section-header">
                            <span class="cadastro-card__section-icon cadastro-card__section-icon--yellow">
                                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            </span>
                            <h2 class="cadastro-card__section-title">Endereço</h2>
                        </div>

                        <div class="cadastro-grid cadastro-grid--endereco">
                            <div class="cadastro-field">
                                <label for="endereco_cep" class="cadastro-label">CEP</label>
                                <input
                                    type="text"
                                    id="endereco_cep"
                                    name="endereco_cep"
                                    class="cadastro-input @error('endereco_cep') is-invalid @enderror"
                                    value="{{ old('endereco_cep') }}"
                                    placeholder="00.000-000"
                                    inputmode="numeric"
                                    maxlength="10"
                                    data-mask="cep"
                                    required
                                >
                                @error('endereco_cep')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="endereco_bairro" class="cadastro-label">Bairro</label>
                                <input
                                    type="text"
                                    id="endereco_bairro"
                                    name="endereco_bairro"
                                    class="cadastro-input @error('endereco_bairro') is-invalid @enderror"
                                    value="{{ old('endereco_bairro') }}"
                                    required
                                >
                                @error('endereco_bairro')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="endereco_logradouro" class="cadastro-label">Logradouro</label>
                                <input
                                    type="text"
                                    id="endereco_logradouro"
                                    name="endereco_logradouro"
                                    class="cadastro-input @error('endereco_logradouro') is-invalid @enderror"
                                    value="{{ old('endereco_logradouro') }}"
                                    required
                                >
                                @error('endereco_logradouro')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="endereco_cidade" class="cadastro-label">Cidade</label>
                                <input
                                    type="text"
                                    id="endereco_cidade"
                                    name="endereco_cidade"
                                    class="cadastro-input @error('endereco_cidade') is-invalid @enderror"
                                    value="{{ old('endereco_cidade', 'Caraguatatuba') }}"
                                    required
                                >
                                @error('endereco_cidade')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="endereco_numero" class="cadastro-label">Número</label>
                                <input
                                    type="text"
                                    id="endereco_numero"
                                    name="endereco_numero"
                                    class="cadastro-input @error('endereco_numero') is-invalid @enderror"
                                    value="{{ old('endereco_numero') }}"
                                    inputmode="numeric"
                                    required
                                >
                                @error('endereco_numero')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="cadastro-field">
                                <label for="endereco_referencia" class="cadastro-label">Ponto de referência (opcional)</label>
                                <input
                                    type="text"
                                    id="endereco_referencia"
                                    name="endereco_referencia"
                                    class="cadastro-input @error('endereco_referencia') is-invalid @enderror"
                                    value="{{ old('endereco_referencia') }}"
                                >
                                @error('endereco_referencia')
                                    <span class="cadastro-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- ==================== TERMO DE USO ==================== --}}
                    <div class="cadastro-termo">
                        <input
                            type="checkbox"
                            id="termo_uso"
                            name="termo_uso"
                            class="cadastro-termo__checkbox"
                            value="1"
                            {{ old('termo_uso') ? 'checked' : '' }}
                            required
                        >
                        <label for="termo_uso" class="cadastro-termo__label">
                            Li e concordo com o <a href="#" target="_blank" rel="noopener">Termo de Uso</a>
                        </label>
                    </div>
                    @error('termo_uso')
                        <span class="cadastro-error d-block mt-1">{{ $message }}</span>
                    @enderror

                    {{-- ==================== AÇÕES ==================== --}}
                    <div class="cadastro-actions">
                        <button type="submit" class="cadastro-btn">Cadastrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
