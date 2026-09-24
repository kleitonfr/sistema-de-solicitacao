{{--
    Parcial do formulário de Entrar do ESIC.
    Reaproveitado tanto pela view completa (esic/entrar.blade.php, no
    primeiro carregamento da página) quanto pelo fragmento servido via AJAX
    ao alternador do painel ESIC (ver FragmentoEsicController) — mesma
    marcação nos dois casos, sem duplicação (DRY).

    Mesmos campos do formulário de Entrar do Sistema 651 (e-mail + senha) —
    outro sistema de acesso, mesmo formato de credencial.
--}}
<div class="cadastro-card entrar">

    @if ($errors->any())
        <div class="cadastro-alert cadastro-alert--erro" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('esic.acesso.store') }}" method="POST" novalidate>
        @csrf
        <div class="cadastro-card__cabecalho-secao">
            <span class="cadastro-card__icone-secao cadastro-card__icone-secao--verde">
                <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
            </span>
            <h2 class="cadastro-card__titulo-secao">ESIC</h2>
        </div>

        <div class="cadastro-field cadastro-field--span-full">
            <label for="esic_email" class="cadastro-label">E-mail</label>
            <input type="email" id="esic_email" name="email" class="cadastro-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder=" " required autocomplete="email">
            @error('email')
                <span class="cadastro-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="cadastro-field cadastro-field--span-full mt-4">
            <label for="esic_senha" class="cadastro-label">Senha</label>
            <input type="password" id="esic_senha" name="senha" class="cadastro-input @error('senha') is-invalid @enderror"
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

</div>
