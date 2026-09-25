{{--
    Parcial do formulário de Cadastro (Sistema 651).
    Reaproveitada tanto pela view completa (portal/cadastro.blade.php, no
    primeiro carregamento da página) quanto pelo fragmento servido via AJAX
    ao alternador (ver FragmentoAcessoController) — mesma marcação nos dois
    casos, sem duplicação (DRY).

    Campos unificam o cadastro original do Portal (Nome Social, Nome da
    Mãe, Data de Nascimento, Sexo, Telefone completo com DDD) com o padrão
    de cadastro do e-SIC (Tipo de Pessoa Física/Jurídica, Faixa Etária,
    Escolaridade, Profissão, confirmação de e-mail, Acesso com senha).

    Nota: todo <input> de texto/email tem placeholder=" " (espaço em
    branco, sem texto visível) propositalmente — é o que ativa a pseudo-
    classe CSS :placeholder-shown, usada em cadastro.css para o label
    "flutuar" apenas quando o campo tem conteúdo real digitado.

    Tipo de Pessoa (Física/Jurídica) alterna os campos seguintes via JS —
    ver alternador-tipo-pessoa.js.
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

    {{-- Tipo de Pessoa — alterna os campos de Física/Jurídica abaixo via JS (ver alternador-tipo-pessoa.js) --}}
    <div class="cadastro-radio-grupo" role="radiogroup" aria-label="Tipo de Pessoa">
        <label class="cadastro-radio">
            <input
                type="radio"
                name="tipo_pessoa"
                value="fisica"
                {{ old('tipo_pessoa', 'fisica') === 'fisica' ? 'checked' : '' }}
                data-alterna-tipo-pessoa="fisica"
            >
            <span>Física</span>
        </label>
        <label class="cadastro-radio">
            <input
                type="radio"
                name="tipo_pessoa"
                value="juridica"
                {{ old('tipo_pessoa') === 'juridica' ? 'checked' : '' }}
                data-alterna-tipo-pessoa="juridica"
            >
            <span>Jurídica</span>
        </label>
    </div>

    <form action="{{ route('cadastro.store') }}" method="POST" novalidate>
        @csrf

        {{-- ==================== DADOS PESSOAIS ==================== --}}
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--vermelho">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Dados Pessoais</h2>
            </div>

            {{-- Campos exibidos quando Tipo de Pessoa = Física --}}
            <div class="cadastro-grid" data-campos-tipo-pessoa="fisica">
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
            </div>

            {{-- Campos exibidos quando Tipo de Pessoa = Jurídica --}}
            <div class="cadastro-grid" data-campos-tipo-pessoa="juridica" hidden>
                <div class="cadastro-field">
                    <label for="razao_social" class="cadastro-label">Razão Social</label>
                    <input
                        type="text"
                        id="razao_social"
                        name="razao_social"
                        class="cadastro-input @error('razao_social') is-invalid @enderror"
                        value="{{ old('razao_social') }}"
                        placeholder=" "
                        autocomplete="organization"
                    >
                    @error('razao_social')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="cnpj" class="cadastro-label">CNPJ</label>
                    <input
                        type="text"
                        id="cnpj"
                        name="cnpj"
                        class="cadastro-input @error('cnpj') is-invalid @enderror"
                        value="{{ old('cnpj') }}"
                        placeholder=" "
                        inputmode="numeric"
                        maxlength="18"
                        data-mask="cnpj"
                    >
                    @error('cnpj')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="cadastro-grid">
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

                <div class="cadastro-field">
                    <label for="faixa_etaria" class="cadastro-label">Faixa Etária</label>
                    <select id="faixa_etaria" name="faixa_etaria" class="cadastro-select @error('faixa_etaria') is-invalid @enderror" required>
                        <option value="" disabled {{ old('faixa_etaria') ? '' : 'selected' }}></option>
                        <option value="ate_17" @selected(old('faixa_etaria') === 'ate_17')>Até 17 anos</option>
                        <option value="18_24" @selected(old('faixa_etaria') === '18_24')>18 a 24 anos</option>
                        <option value="25_34" @selected(old('faixa_etaria') === '25_34')>25 a 34 anos</option>
                        <option value="35_44" @selected(old('faixa_etaria') === '35_44')>35 a 44 anos</option>
                        <option value="45_59" @selected(old('faixa_etaria') === '45_59')>45 a 59 anos</option>
                        <option value="60_mais" @selected(old('faixa_etaria') === '60_mais')>60 anos ou mais</option>
                    </select>
                    @error('faixa_etaria')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="escolaridade" class="cadastro-label">Escolaridade</label>
                    <select id="escolaridade" name="escolaridade" class="cadastro-select @error('escolaridade') is-invalid @enderror" required>
                        <option value="" disabled {{ old('escolaridade') ? '' : 'selected' }}></option>
                        <option value="fundamental" @selected(old('escolaridade') === 'fundamental')>Ensino Fundamental</option>
                        <option value="medio" @selected(old('escolaridade') === 'medio')>Ensino Médio</option>
                        <option value="superior" @selected(old('escolaridade') === 'superior')>Ensino Superior</option>
                        <option value="pos_graduacao" @selected(old('escolaridade') === 'pos_graduacao')>Pós-graduação</option>
                        <option value="nao_informar" @selected(old('escolaridade') === 'nao_informar')>Prefiro não informar</option>
                    </select>
                    @error('escolaridade')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="profissao" class="cadastro-label">Profissão (opcional)</label>
                    <input
                        type="text"
                        id="profissao"
                        name="profissao"
                        class="cadastro-input @error('profissao') is-invalid @enderror"
                        value="{{ old('profissao') }}"
                        placeholder=" "
                    >
                    @error('profissao')
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
                    <label for="email_confirmacao" class="cadastro-label">Confirme o E-mail</label>
                    <input
                        type="email"
                        id="email_confirmacao"
                        name="email_confirmacao"
                        class="cadastro-input @error('email_confirmacao') is-invalid @enderror"
                        value="{{ old('email_confirmacao') }}"
                        placeholder=" "
                        required
                        autocomplete="email"
                    >
                    @error('email_confirmacao')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
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

                <div class="cadastro-field-inline">
                    <div class="cadastro-field">
                        <label for="endereco_cidade" class="cadastro-label">Cidade</label>
                        <input
                            type="text"
                            id="endereco_cidade"
                            name="endereco_cidade"
                            class="cadastro-input @error('endereco_cidade') is-invalid @enderror"
                            value="{{ old('endereco_cidade') }}"
                            placeholder=" "
                            required
                        >
                        @error('endereco_cidade')
                            <span class="cadastro-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="cadastro-field">
                        <label for="endereco_uf" class="cadastro-label">UF</label>
                        <select id="endereco_uf" name="endereco_uf" class="cadastro-select @error('endereco_uf') is-invalid @enderror" required>
                            <option value="" disabled {{ old('endereco_uf') ? '' : 'selected' }}></option>
                            <option value="SP" @selected(old('endereco_uf') === 'SP')>SP</option>
                            <option value="RJ" @selected(old('endereco_uf') === 'RJ')>RJ</option>
                            <option value="MG" @selected(old('endereco_uf') === 'MG')>MG</option>
                            <option value="PR" @selected(old('endereco_uf') === 'PR')>PR</option>
                            <option value="SC" @selected(old('endereco_uf') === 'SC')>SC</option>
                            <option value="RS" @selected(old('endereco_uf') === 'RS')>RS</option>
                        </select>
                        @error('endereco_uf')
                            <span class="cadastro-error">{{ $message }}</span>
                        @enderror
                    </div>
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
                    <label for="endereco_complemento" class="cadastro-label">Complemento (opcional)</label>
                    <input
                        type="text"
                        id="endereco_complemento"
                        name="endereco_complemento"
                        class="cadastro-input @error('endereco_complemento') is-invalid @enderror"
                        value="{{ old('endereco_complemento') }}"
                        placeholder=" "
                    >
                    @error('endereco_complemento')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ==================== ACESSO ==================== --}}
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--vermelho">
                    <i class="fa-solid fa-lock" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Acesso</h2>
            </div>

            <p class="cadastro-texto-auxiliar">O acesso é feito com o e-mail informado acima.</p>

            <div class="cadastro-grid">
                <div class="cadastro-field">
                    <label for="senha" class="cadastro-label">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="cadastro-input @error('senha') is-invalid @enderror"
                        placeholder=" "
                        required
                        autocomplete="new-password"
                    >
                    @error('senha')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="senha_confirmacao" class="cadastro-label">Confirme a Senha</label>
                    <input
                        type="password"
                        id="senha_confirmacao"
                        name="senha_confirmacao"
                        class="cadastro-input @error('senha_confirmacao') is-invalid @enderror"
                        placeholder=" "
                        required
                        autocomplete="new-password"
                    >
                    @error('senha_confirmacao')
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
