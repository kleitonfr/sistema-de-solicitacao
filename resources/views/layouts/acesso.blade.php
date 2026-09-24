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

    <div class="acesso-split @yield('classe_modo_esic')">

        {{-- ==================== PAINEL ESQUERDO — FORMULÁRIO (Sistema 651) ==================== --}}
        <div class="acesso-split__form-pane">
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
                        data-fragmento-url="{{ route('fragmentos.cadastro') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('cadastro.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('cadastro.*')) aria-current="page" @endif
                    >
                        Cadastrar
                    </a>
                    <span class="acesso-alternador__indicador {{ request()->routeIs('cadastro.*') ? 'is-right' : '' }}" aria-hidden="true"></span>
                </nav>

                <div id="acesso-conteudo-formulario" class="acesso-conteudo-formulario">
                    @yield('content')
                </div>

            </div>
        </div>

        {{-- ==================== PAINEL DIREITO — MARCA / INSTITUCIONAL (desliza para o modo ESIC) ==================== --}}
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


                <a
                    href="{{ request()->routeIs('esic.*') ? route('acesso.index') : route('esic.acesso.index') }}"
                    id="acesso-toggle-esic"
                    data-fragmento-esic-entrar="{{ route('fragmentos.esic.entrar') }}"
                    class="acesso-marca__esic"
                >
                    <i class="fa-solid fa-arrow-left acesso-marca__esic-icone acesso-marca__esic-icone--voltar" aria-hidden="true"></i>
                    <span class="acesso-marca__esic-texto acesso-marca__esic-texto--ir">Acesse o ESIC por aqui</span>
                    <span class="acesso-marca__esic-texto acesso-marca__esic-texto--voltar">Acesse o Portal 156</span>
                    <i class="fa-solid fa-arrow-right acesso-marca__esic-icone acesso-marca__esic-icone--ir" aria-hidden="true"></i>
                </a>

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

        {{-- ==================== PAINEL ESIC — aparece quando o painel de marca desliza ==================== --}}
        <div class="acesso-split__esic-pane">
            <div class="acesso-split__esic-inner">

                {{-- Alternador Entrar / Cadastrar do ESIC --}}
                <nav class="acesso-alternador" aria-label="Alternar entre entrar e cadastro no ESIC">
                    <a
                        href="{{ route('esic.acesso.index') }}"
                        data-fragmento-url="{{ route('fragmentos.esic.entrar') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('esic.acesso.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('esic.acesso.*')) aria-current="page" @endif
                    >
                        Entrar
                    </a>
                    <a
                        href="{{ route('esic.cadastro.index') }}"
                        data-fragmento-url="{{ route('fragmentos.esic.cadastro') }}"
                        class="acesso-alternador__opcao {{ request()->routeIs('esic.cadastro.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('esic.cadastro.*')) aria-current="page" @endif
                    >
                        Cadastrar
                    </a>
                    <span class="acesso-alternador__indicador {{ request()->routeIs('esic.cadastro.*') ? 'is-right' : '' }}" aria-hidden="true"></span>
                </nav>

                <div id="esic-conteudo-formulario" class="acesso-conteudo-formulario">
                    @yield('content-esic')
                </div>

            </div>
        </div>

    </div>

</body>

</html>
