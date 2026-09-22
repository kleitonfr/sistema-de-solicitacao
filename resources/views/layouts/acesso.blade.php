<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema 651 — Prefeitura Municipal de Caraguatatuba')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="acesso-body">

    <div class="acesso-split">

        {{-- ==================== PAINEL ESQUERDO — FORMULÁRIO ==================== --}}
        <div class="acesso-split__form-pane">
            <div class="acesso-split__form-inner">

                {{-- Alternador Entrar / Cadastrar --}}
                <nav class="acesso-alternador" aria-label="Alternar entre entrar e cadastro">
                    <a
                        href="{{ route('acesso.index') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('acesso.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('acesso.*')) aria-current="page" @endif
                    >
                        Entrar
                    </a>
                    <a
                        href="{{ route('cadastro.index') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('cadastro.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('cadastro.*')) aria-current="page" @endif
                    >
                        Cadastrar
                    </a>
                    <span class="acesso-alternador__indicador {{ request()->routeIs('cadastro.*') ? 'is-right' : '' }}" aria-hidden="true"></span>
                </nav>

                @yield('content')

            </div>
        </div>

        {{-- ==================== PAINEL DIREITO — MARCA / INSTITUCIONAL ==================== --}}
        <aside class="acesso-split__marca-pane" aria-label="Identificação do sistema">
            <div class="acesso-split__marca-inner">

                <div class="acesso-marca__identificacao">
                    <div class="acesso-marca__nome-sistema">
                        <img
                            src="{{ asset('assets/img/prefeitura-de-caraguatatuba.png') }}"
                            alt="Brasão de Caraguatatuba"
                            class="acesso-marca__brasao"
                            onerror="this.style.display='none'"
                        >
                        <img
                            src="{{ asset('assets/img/letreiro.png') }}"
                            alt="Logo da Prefeitura de Caraguatatuba 'Caraguatatuba em Tempo de Prosperidade'"
                            class="acesso-marca__logo"
                            onerror="this.style.display='none'"
                        >
                    </div>
                    
                    <p class="acesso-marca__frase">
                        Acesso unificado aos serviços digitais
                    </p>
                    <p class="acesso-marca__subfrase">
                        do município de Caraguatatuba
                    </p>

                </div>


                <div class="acesso-marca__contato">
                    <div class="acesso-marca__contato-item">
                        <span class="acesso-marca__contato-rotulo">Prefeitura</span>
                        <span class="acesso-marca__contato-valor">(12) 3897-8100</span>
                    </div>
                    <div class="acesso-marca__contato-item">
                        <span class="acesso-marca__contato-rotulo">Ouvidoria/SIC</span>
                        <span class="acesso-marca__contato-valor">0800-770-0678</span>
                    </div>
                    <div class="acesso-marca__contato-item">
                        <span class="acesso-marca__contato-rotulo">Ouvidoria Saúde</span>
                        <span class="acesso-marca__contato-valor">0800-779-4545</span>
                    </div>
                </div>

                <p class="acesso-marca__direitos-autorais">
                    &copy; {{ date('Y') }} · Prefeitura Municipal de Caraguatatuba · Todos os direitos reservados
                </p>

            </div>
        </aside>

    </div>

</body>

</html>
