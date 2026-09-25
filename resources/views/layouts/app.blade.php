<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Prefeitura Municipal de Caraguatatuba')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app-body">

    <header id="cabecalho" class="app-header">

     <div class="app-header__accent-bar app-header__accent-bar--red"></div>
     <div class="app-header__accent-bar app-header__accent-bar--yellow"></div>
     <div class="app-header__accent-bar app-header__accent-bar--green"></div>

        <div class="app-header__bar container">
            

            {{-- Brasão --}}
            <div class="app-header__coat">
                <img src="{{ asset('assets/img/prefeitura-de-caraguatatuba.png') }}" alt="Brasão de Caraguatatuba"
                    class="app-header__coat-img" onerror="this.style.display='none'">
                <span class="app-header__coat-text">
                    PREFEITURA DE<br><strong>CARAGUATATUBA</strong>
                </span>
            </div>


            {{-- Menu navegação (desktop) --}}
            <nav class="app-header__nav" aria-label="Menu principal">
                <a href="#" id="btn-inicio-desktop" class="app-header__nav-link">Início</a>
                <a href="{{ route('servicos.index') }}" id="btn-servicos-desktop" class="app-header__nav-link {{ request()->routeIs('servicos.*') ? 'is-active' : '' }}">Serviços</a>
                <a href="{{ route('acesso.index') }}" id="btn-login-desktop" class="app-header__nav-link {{ request()->routeIs('acesso.*') ? 'is-active' : '' }}">Login</a>
                <a href="{{ route('cadastro.index') }}" id="btn-cadastro-desktop"
                    class="app-header__nav-link {{ request()->routeIs('cadastro.*') ? 'is-active' : '' }}">
                    Cadastre-se
                </a>
                <a href="#" id="btn-acesso-rapido-desktop" class="app-header__nav-link app-header__nav-link--icon">
                    Acesso Rápido
                    <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                </a>
            </nav>


            {{-- Menu mobile (toggle) --}}
            <label class="app-header__burger" aria-label="Abrir menu de navegação" role="button" aria-expanded="false"
                id="btn-menu-mobile">
                <input class="d-none" type="checkbox"
                    onclick="const menu = document.getElementById('mobile-menu'); menu.classList.toggle('d-none'); const btn = document.getElementById('btn-menu-mobile'); btn.setAttribute('aria-expanded', !menu.classList.contains('d-none'));" />
                <div aria-hidden="true" class="app-header__burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </label>
        </div>

        {{-- Menu expansível para mobile --}}
        <nav id="mobile-menu" aria-label="Menu mobile" class="app-header__mobile-nav d-none">
            <a href="#" id="btn-inicio-mobile" class="app-header__mobile-link">Início</a>
            <a href="{{ route('servicos.index') }}" id="btn-servicos-mobile" class="app-header__mobile-link {{ request()->routeIs('servicos.*') ? 'is-active' : '' }}">Serviços</a>
            <a href="{{ route('acesso.index') }}" id="btn-login-mobile" class="app-header__mobile-link {{ request()->routeIs('acesso.*') ? 'is-active' : '' }}">Login</a>
            <a href="{{ route('cadastro.index') }}" id="btn-cadastro-mobile"
                class="app-header__mobile-link {{ request()->routeIs('cadastro.*') ? 'is-active' : '' }}">
                Cadastre-se
            </a>
            <a href="#" id="btn-acesso-rapido-mobile" class="app-header__mobile-link">Acesso Rápido</a>
        </nav>
    </header>

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

    {{-- Conteúdo principal --}}
    <main id="conteudoPrincipal" class="app-main" aria-label="Conteúdo principal">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer id="rodape" class="app-footer">

        <div class="app-footer__coat-row">
            <a href="https://www.caraguatatuba.sp.gov.br/" title="Prefeitura de Caraguatatuba">
                <img src="{{ asset('assets/img/logo-branco.png') }}" class="app-footer__coat-img"
                    alt="Prefeitura Municipal de Caraguatatuba" onerror="this.style.display='none'">
            </a>
        </div>

        <div class="app-footer__columns container">

            <div class="app-footer__column">
                <h6 class="app-footer__column-title">Principais Serviços</h6>
                <ul class="app-footer__link-list">
                    <li><a href="https://www.caraguatatuba.sp.gov.br/pmc/agenda-do-prefeito">Agenda do Prefeito</a></li>
                    <li><a href="https://www.caraguatatuba.sp.gov.br/pmc/category/diario-oficial/">Diário Oficial</a>
                    </li>
                    <li><a href="https://fundacc.sp.gov.br/">Fundacc</a></li>
                    <li><a href="https://portaldatransparencia.caraguatatuba.sp.gov.br/home">Transparência</a></li>
                    <li><a href="https://www.caragua.tur.br/">Turismo</a></li>
                </ul>
            </div>

            <div class="app-footer__column">
                <h6 class="app-footer__column-title">Cidadão</h6>
                <ul class="app-footer__link-list">
                    <li><a
                            href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-ao-cidadao/concursos-e-processos-seletivos/">Concursos</a>
                    </li>
                    <li><a href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-ao-cidadao/saude/">Saúde</a>
                    </li>
                    <li><a
                            href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-ao-cidadao/social/">Social</a>
                    </li>
                    <li><a
                            href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-ao-cidadao/consultas/">Tributos</a>
                    </li>
                    <li><a
                            href="https://pmcaraguatatuba.geosiap.net.br/pmcaraguatatuba/websis/siapegov/administrativo/gpro/gpro_index.php">Portal
                            do Cidadão</a></li>
                </ul>
            </div>

            <div class="app-footer__column">
                <h6 class="app-footer__column-title">Empresas</h6>
                <ul class="app-footer__link-list">
                    <li><a href="https://www.comprascaragua.com.br/home.jsf?windowId=490">Portal de Compras</a></li>
                    <li><a
                            href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-a-empresa/licitacoes/">Licitações</a>
                    </li>
                    <li><a
                            href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-a-empresa/licenciamento-ambiental/">Lic.
                            Ambiental</a></li>
                    <li><a href="https://www.caraguatatuba.sp.gov.br/pmc/servicos/servicos-a-empresa/plano-diretor/">Plano
                            Diretor</a></li>
                </ul>
            </div>

            <div class="app-footer__column">
                <h6 class="app-footer__column-title">Mais</h6>
                <ul class="app-footer__link-list">
                    <li><a href="https://www.caraguatatuba.sp.gov.br/pmc/governo-municipal/">Governo Municipal</a></li>
                    <li><a href="https://caraguatatuba.legislacaocompilada.com.br/">Legislação</a></li>
                    <li><a href="https://mail.caraguatatuba.sp.gov.br/">WebMail</a></li>
                    <li><a href="https://www.caraguatatuba.sp.gov.br/pmc/category/clipping/">Clipping</a></li>
                </ul>
            </div>

            <div class="app-footer__column">
                <h6 class="app-footer__column-title">Telefones Úteis</h6>
                <ul class="app-footer__phone-list">
                    <li>Prefeitura<br><strong>(12) 3897-8100</strong></li>
                    <li>Ouvidoria/SIC<br><strong>0800-770-0678</strong></li>
                    <li>Ouvidoria Saúde<br><strong>0800-779-4545</strong></li>
                </ul>
            </div>

        </div>

        <div class="app-footer__bottom">
            <div class="app-footer__bottom-inner container">
                <div class="app-footer__socials">
                    <a href="https://www.facebook.com/prefeituradecaraguatatuba" target="_blank"
                        rel="noopener noreferrer" title="Facebook (abre em nova aba)"
                        aria-label="Facebook da Prefeitura (abre em nova aba)">
                        <i class="fa-brands fa-square-facebook" aria-hidden="true"></i>
                    </a>
                    <a href="https://www.instagram.com/caraguatatuba_oficial/" target="_blank" rel="noopener noreferrer"
                        title="Instagram (abre em nova aba)" aria-label="Instagram da Prefeitura (abre em nova aba)">
                        <i class="fa-brands fa-square-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="https://www.youtube.com/channel/UCH84Ukn-PabhE7vhXxhPUDw" target="_blank"
                        rel="noopener noreferrer" title="YouTube (abre em nova aba)"
                        aria-label="YouTube da Prefeitura (abre em nova aba)">
                        <i class="fa-brands fa-square-youtube" aria-hidden="true"></i>
                    </a>
                    <a href="https://www.flickr.com/photos/prefeituracaraguatatuba/albums/" target="_blank"
                        rel="noopener noreferrer" title="Flickr (abre em nova aba)"
                        aria-label="Galeria de fotos no Flickr (abre em nova aba)">
                        <i class="fa-brands fa-flickr" aria-hidden="true"></i>
                    </a>
                </div>

                <p class="app-footer__copyright">
                    &copy; {{ date('Y') }} · Prefeitura Municipal de Caraguatatuba · Todos os direitos reservados
                </p>

            </div>
        </div>
    </footer>

    @stack('scripts')

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