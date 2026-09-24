<div class="cadastro-card entrar">

    @if ($errors->any())
        <div class="cadastro-alert cadastro-alert--erro" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('acesso.store') }}" method="POST" novalidate>
        @csrf
        <div class="cadastro-card__cabecalho-secao">
            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--verde">
                <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
            </span>
            <h2 class="cadastro-card__titulo-secao">Sistema 651</h2>
        </div>

        <div class="cadastro-field cadastro-field--span-full">
            <label for="email" class="cadastro-label">E-mail</label>
            <input type="email" id="email" name="email" class="cadastro-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder=" " required autocomplete="email" autofocus>
            @error('email')
                <span class="cadastro-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="cadastro-field cadastro-field--span-full mt-4">
            <label for="senha" class="cadastro-label">Senha</label>
            <input type="password" id="senha" name="senha" class="cadastro-input @error('senha') is-invalid @enderror"
                placeholder=" " required autocomplete="current-password">
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

    <a href="#" class="acesso-denuncia-anonima">
        <span class="acesso-denuncia-anonima__icone">
            <i class="fa-solid fa-user-secret" aria-hidden="true"></i>
        </span>
        <span class="acesso-denuncia-anonima__texto">Prefere não se identificar? Faça uma denúncia anônima</span>
    </a>


</div>
