{{--
    Parcial do formulário de Cadastro.
    Reaproveitado tanto pela view completa (cadastro/index.blade.php, no
    primeiro carregamento da página) quanto pelo fragmento servido via AJAX
    ao alternador (ver FragmentoAcessoController) — mesma marcação nos dois
    casos, sem duplicação (DRY).

    Nota: todo <input> de texto/email tem placeholder=" " (espaço em
    branco, sem texto visível) propositalmente — é o que ativa a pseudo-
    classe CSS :placeholder-shown, usada em cadastro.css para o label
    "flutuar" apenas quando o campo tem conteúdo real digitado.
--}}
<div class="cadastro-card">

    {{-- Alertas de retorno da submissão (simulados até a API existir) --}}
    @if (session('situacao') === 'sucesso')
        <div class="cadastro-alert cadastro-alert--sucesso" role="status">
            {{ session('mensagem') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="cadastro-alert cadastro-alert--erro" role="alert">
            Verifique os campos destacados abaixo e tente novamente.
        </div>
    @endif

    <form action="{{ route('cadastro.store') }}" method="POST" novalidate>
        @csrf

        {{-- ==================== DADOS DO SOLICITANTE ==================== --}}
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--vermelho">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Dados do Solicitante</h2>
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
                        placeholder=" "
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
                        placeholder=" "
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
                        placeholder=" "
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
                        placeholder=" "
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
                        placeholder=" "
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
                            <option value="" disabled {{ old('sexo') ? '' : 'selected' }}></option>
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
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--verde">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Telefone</h2>
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
                        <option value="" disabled {{ old('telefone_tipo') ? '' : 'selected' }}></option>
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
                        placeholder=" "
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
                        placeholder=" "
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
                        placeholder=" "
                    >
                    @error('telefone_observacao')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ==================== ENDEREÇO ==================== --}}
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--amarelo">
                    <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Endereço</h2>
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
                        placeholder=" "
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
                        placeholder=" "
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
                        placeholder=" "
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
                        value="{{ old('endereco_cidade', '') }}"
                        placeholder=" "
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
                        placeholder=" "
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
                        placeholder=" "
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
