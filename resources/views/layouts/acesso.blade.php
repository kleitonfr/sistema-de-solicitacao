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

            <div class="acesso-split__bar acesso-split-header__accent-bar--red"></div>
            <div class="acesso-split__bar acesso-split-header__accent-bar--yellow"></div>
            <div class="acesso-split__bar acesso-split-header__accent-bar--green"></div>

            <div class="acesso-split__form-inner">

                {{-- Alternador Entrar / Cadastrar --}}
                <nav class="acesso-alternador" aria-label="Alternar entre entrar e cadastro">
                    <a
                        href="{{ route('acesso.index') }}"
                        data-fragmento-url="{{ route('fragmentos.entrar') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('acesso.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('acesso.*')) aria-current="page" @endif
                    >
                        Entrar
                    </a>
                    <a
                        href="{{ route('cadastro.index') }}"
                        data-fragmento-url="{{ route('fragmentos.cadastro.fisica') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('cadastro.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('cadastro.*')) aria-current="page" @endif
                    >
                        Cadastrar
                    </a>
                    <span class="acesso-alternador__indicador {{ request()->routeIs('cadastro.*') ? 'is-right' : '' }}" aria-hidden="true"></span>
                </nav>

                {{-- As URLs dos formulários de cadastro ficam no contêiner (e não nas
                     parciais) porque ele nunca é substituído, só o innerHTML dele: os
                     dados estão no DOM em qualquer tela (Entrar, Cadastrar Física ou
                     Jurídica). Lidas por alternador-tipo-pessoa.js. --}}
                <div id="acesso-conteudo-formulario" class="acesso-conteudo-formulario"
                     data-url-cadastro-fisica="{{ route('fragmentos.cadastro.fisica') }}"
                     data-url-cadastro-juridica="{{ route('fragmentos.cadastro.juridica') }}">
                    @yield('content', view('portal.parciais.formulario-entrar'))
                </div>

            </div>
        </div>

        {{-- ==================== PAINEL DIREITO — MARCA / INSTITUCIONAL ==================== --}}
        <aside class="acesso-split__marca-pane" aria-label="Identificação do sistema">
            <img
                src="{{ asset('assets/img/bkg-01.jpg') }}"
                alt=""
                class="acesso-split__marca-fundo"
                aria-hidden="true"
            >
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

    {{-- Acessibilidade --}}
    <div id="acessibilidade-widget">
        <button id="btn-abrir-acessibilidade" title="Acessibilidade" aria-label="Abrir ferramentas de acessibilidade"
            aria-expanded="false">
            <i class="fa-solid fa-universal-access" aria-hidden="true"></i>
        </button>
        <div id="painel-acessibilidade">
            <button id="btn-aumentar" title="Aumentar fonte" aria-label="Aumentar tamanho da fonte">
                <span>A+</span>
            </button>
            <button id="btn-diminuir" title="Diminuir fonte" aria-label="Diminuir tamanho da fonte">
                <span>A-</span>
            </button>
            <div class="app-a11y-panel__divider"></div>
            <button id="btn-contraste" title="Alto contraste" aria-label="Alternar alto contraste" aria-pressed="false">
                <span><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i></span>
            </button>
        </div>
    </div>


    <a href="#" class="acesso-denuncia-anonima">
        <span class="acesso-denuncia-anonima__icone">
            <i class="fa-solid fa-user-secret" aria-hidden="true"></i>
        </span>
        <span class="acesso-denuncia-anonima__texto">Prefere não se identificar? Faça uma denúncia anônima</span>
    </a>

    {{-- VLibras --}}
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper acessibilidade-widget"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>new window.VLibras.Widget('https://vlibras.gov.br/app');</script>

</body>

</html>
