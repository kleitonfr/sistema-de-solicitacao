{{--
    Parcial do formulário de Cadastro do ESIC.
    Reaproveitado tanto pela view completa (esic/cadastro.blade.php, no
    primeiro carregamento da página) quanto pelo fragmento servido via AJAX
    ao alternador do painel ESIC (ver FragmentoEsicController) — mesma
    marcação nos dois casos, sem duplicação (DRY).

    Campos baseados no cadastro real do e-SIC (sistema legado): Tipo de
    Pessoa (Física/Jurídica, alterna os campos seguintes), Dados Pessoais,
    Endereço e Acesso ao e-SIC (login pelo e-mail, sem usuário separado).
--}}
<div class="cadastro-card">

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

    <form action="{{ route('esic.cadastro.store') }}" method="POST" novalidate>
        @csrf

        {{-- ==================== DADOS PESSOAIS ==================== --}}
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--vermelho">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Dados Pessoais</h2>
            </div>

            {{-- Tipo de Pessoa — alterna os campos de Física/Jurídica abaixo via JS (ver alternador-tipo-pessoa-esic.js) --}}
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

            {{-- Campos exibidos quando Tipo de Pessoa = Física --}}
            <div class="cadastro-grid" data-campos-tipo-pessoa="fisica">
                <div class="cadastro-field">
                    <label for="esic_nome" class="cadastro-label">Nome</label>
                    <input
                        type="text"
                        id="esic_nome"
                        name="nome"
                        class="cadastro-input @error('nome') is-invalid @enderror"
                        value="{{ old('nome') }}"
                        placeholder=" "
                        required
                        autocomplete="name"
                    >
                    @error('nome')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="esic_cpf" class="cadastro-label">CPF</label>
                    <input
                        type="text"
                        id="esic_cpf"
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
                    <label for="esic_razao_social" class="cadastro-label">Razão Social</label>
                    <input
                        type="text"
                        id="esic_razao_social"
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
                    <label for="esic_cnpj" class="cadastro-label">CNPJ</label>
                    <input
                        type="text"
                        id="esic_cnpj"
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
                    <label for="esic_faixa_etaria" class="cadastro-label">Faixa Etária (opcional)</label>
                    <select id="esic_faixa_etaria" name="faixa_etaria" class="cadastro-select @error('faixa_etaria') is-invalid @enderror">
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
                    <label for="esic_escolaridade" class="cadastro-label">Escolaridade (opcional)</label>
                    <select id="esic_escolaridade" name="escolaridade" class="cadastro-select @error('escolaridade') is-invalid @enderror">
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
                    <label for="esic_profissao" class="cadastro-label">Profissão (opcional)</label>
                    <input
                        type="text"
                        id="esic_profissao"
                        name="profissao"
                        class="cadastro-input @error('profissao') is-invalid @enderror"
                        value="{{ old('profissao') }}"
                        placeholder=" "
                    >
                    @error('profissao')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field-inline">
                    <div class="cadastro-field">
                        <label for="esic_telefone_tipo" class="cadastro-label">Tipo Telefone (opcional)</label>
                        <select id="esic_telefone_tipo" name="telefone_tipo" class="cadastro-select @error('telefone_tipo') is-invalid @enderror">
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
                        <label for="esic_telefone" class="cadastro-label">Telefone (opcional)</label>
                        <input
                            type="text"
                            id="esic_telefone"
                            name="telefone"
                            class="cadastro-input @error('telefone') is-invalid @enderror"
                            value="{{ old('telefone') }}"
                            placeholder=" "
                            inputmode="numeric"
                            maxlength="15"
                            data-mask="telefone"
                        >
                        @error('telefone')
                            <span class="cadastro-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="cadastro-field">
                    <label for="esic_email" class="cadastro-label">E-mail</label>
                    <input
                        type="email"
                        id="esic_email"
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
                    <label for="esic_email_confirmacao" class="cadastro-label">Confirme o E-mail</label>
                    <input
                        type="email"
                        id="esic_email_confirmacao"
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
                    <label for="esic_cep" class="cadastro-label">CEP (opcional)</label>
                    <input
                        type="text"
                        id="esic_cep"
                        name="cep"
                        class="cadastro-input @error('cep') is-invalid @enderror"
                        value="{{ old('cep') }}"
                        placeholder=" "
                        inputmode="numeric"
                        maxlength="10"
                        data-mask="cep"
                    >
                    @error('cep')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="esic_logradouro" class="cadastro-label">Logradouro (opcional)</label>
                    <input
                        type="text"
                        id="esic_logradouro"
                        name="logradouro"
                        class="cadastro-input @error('logradouro') is-invalid @enderror"
                        value="{{ old('logradouro') }}"
                        placeholder=" "
                    >
                    @error('logradouro')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="esic_bairro" class="cadastro-label">Bairro (opcional)</label>
                    <input
                        type="text"
                        id="esic_bairro"
                        name="bairro"
                        class="cadastro-input @error('bairro') is-invalid @enderror"
                        value="{{ old('bairro') }}"
                        placeholder=" "
                    >
                    @error('bairro')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field-inline">
                    <div class="cadastro-field">
                        <label for="esic_cidade" class="cadastro-label">Cidade (opcional)</label>
                        <input
                            type="text"
                            id="esic_cidade"
                            name="cidade"
                            class="cadastro-input @error('cidade') is-invalid @enderror"
                            value="{{ old('cidade') }}"
                            placeholder=" "
                        >
                        @error('cidade')
                            <span class="cadastro-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="cadastro-field">
                        <label for="esic_uf" class="cadastro-label">UF (opcional)</label>
                        <select id="esic_uf" name="uf" class="cadastro-select @error('uf') is-invalid @enderror">
                            <option value="" disabled {{ old('uf') ? '' : 'selected' }}></option>
                            <option value="SP" @selected(old('uf') === 'SP')>SP</option>
                            <option value="RJ" @selected(old('uf') === 'RJ')>RJ</option>
                            <option value="MG" @selected(old('uf') === 'MG')>MG</option>
                            <option value="PR" @selected(old('uf') === 'PR')>PR</option>
                            <option value="SC" @selected(old('uf') === 'SC')>SC</option>
                            <option value="RS" @selected(old('uf') === 'RS')>RS</option>
                        </select>
                        @error('uf')
                            <span class="cadastro-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="cadastro-field">
                    <label for="esic_numero" class="cadastro-label">Número (opcional)</label>
                    <input
                        type="text"
                        id="esic_numero"
                        name="numero"
                        class="cadastro-input @error('numero') is-invalid @enderror"
                        value="{{ old('numero') }}"
                        placeholder=" "
                        inputmode="numeric"
                    >
                    @error('numero')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="cadastro-field">
                    <label for="esic_complemento" class="cadastro-label">Complemento (opcional)</label>
                    <input
                        type="text"
                        id="esic_complemento"
                        name="complemento"
                        class="cadastro-input @error('complemento') is-invalid @enderror"
                        value="{{ old('complemento') }}"
                        placeholder=" "
                    >
                    @error('complemento')
                        <span class="cadastro-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </section>

        {{-- ==================== ACESSO AO E-SIC ==================== --}}
        <section class="cadastro-card__secao">
            <div class="cadastro-card__cabecalho-secao">
                <span class="cadastro-card__icone-secao cadastro-card__icone-secao--verde">
                    <i class="fa-solid fa-lock" aria-hidden="true"></i>
                </span>
                <h2 class="cadastro-card__titulo-secao">Acesso ao e-SIC</h2>
            </div>

            <p class="cadastro-texto-auxiliar">O acesso ao e-SIC é feito com o e-mail informado acima.</p>

            <div class="cadastro-grid">
                <div class="cadastro-field">
                    <label for="esic_senha" class="cadastro-label">Senha</label>
                    <input
                        type="password"
                        id="esic_senha"
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
                    <label for="esic_senha_confirmacao" class="cadastro-label">Confirme a Senha</label>
                    <input
                        type="password"
                        id="esic_senha_confirmacao"
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

        <div class="cadastro-actions">
            <button type="submit" class="cadastro-btn">Cadastrar</button>
        </div>
    </form>

</div>
