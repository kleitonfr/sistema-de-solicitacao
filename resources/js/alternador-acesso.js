/**
 * Alternador Entrar/Cadastrar — Sistema 651.
 *
 * Ao clicar em "Entrar" ou "Cadastrar", busca o fragmento HTML do
 * formulário correspondente via requisição AJAX (fetch) e substitui o
 * conteúdo do painel sem recarregar a página, com uma transição de fade
 * suave. A URL do navegador é atualizada via history.pushState,
 * preservando o comportamento do botão voltar/avançar e a possibilidade
 * de compartilhar o link direto de cada formulário.
 *
 * Rotas consumidas (ver routes/web.php): fragmentos.entrar e
 * fragmentos.cadastro.fisica — servidas por FragmentoAcessoController,
 * endpoint dedicado que devolve só o HTML do formulário, sem o layout
 * ao redor. O botão "Cadastrar" sempre aponta para o fragmento de Pessoa
 * Física (o padrão); a troca para Jurídica acontece depois, dentro do
 * próprio formulário de cadastro (ver alternador-tipo-pessoa.js).
 */
document.addEventListener('DOMContentLoaded', () => {
    const alternador = document.querySelector('.acesso-alternador');
    const conteudo = document.getElementById('acesso-conteudo-formulario');
    const indicador = document.querySelector('.acesso-alternador__indicador');

    if (!alternador || !conteudo || !indicador) {
        return;
    }

    let trocaEmAndamento = false;

    alternador.addEventListener('click', (evento) => {
        const opcaoClicada = evento.target.closest('.acesso-alternador__opcao');

        if (!opcaoClicada) {
            return;
        }

        evento.preventDefault();
        trocarFormulario(opcaoClicada);
    });

    window.addEventListener('popstate', () => {
        window.location.reload();
    });

    async function trocarFormulario(opcaoClicada) {
        if (trocaEmAndamento || opcaoClicada.classList.contains('is-active')) {
            return;
        }

        const fragmentoUrl = opcaoClicada.dataset.fragmentoUrl;
        const paginaUrl = opcaoClicada.getAttribute('href');

        if (!fragmentoUrl || !paginaUrl) {
            return;
        }

        trocaEmAndamento = true;

        try {
            const html = await buscarFragmento(fragmentoUrl);

            conteudo.classList.add('esta-trocando');
            await aguardarFimDaTransicao(conteudo);

            conteudo.innerHTML = html;
            conteudo.classList.remove('esta-trocando');

            atualizarEstadoAtivo(opcaoClicada);
            history.pushState({}, '', paginaUrl);
        } catch (erro) {
            // Sem integração com backend de log neste projeto (frontend puro);
            // navega normalmente como reserva, garantindo que o usuário
            // sempre consiga trocar de formulário mesmo se o AJAX falhar.
            window.location.href = paginaUrl;
        } finally {
            trocaEmAndamento = false;
        }
    }

    async function buscarFragmento(fragmentoUrl) {
        const resposta = await fetch(fragmentoUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!resposta.ok) {
            throw new Error('Falha ao carregar o formulário.');
        }

        return resposta.text();
    }

    function atualizarEstadoAtivo(opcaoClicada) {
        const opcoes = alternador.querySelectorAll('.acesso-alternador__opcao');

        opcoes.forEach((opcao) => {
            const estaAtiva = opcao === opcaoClicada;
            opcao.classList.toggle('is-active', estaAtiva);

            if (estaAtiva) {
                opcao.setAttribute('aria-current', 'page');
            } else {
                opcao.removeAttribute('aria-current');
            }
        });

        const ehCadastro = opcaoClicada.dataset.fragmentoUrl.includes('cadastro');
        indicador.classList.toggle('is-right', ehCadastro);
    }

    function aguardarFimDaTransicao(elemento) {
        return new Promise((resolver) => {
            const duracaoMs = 180; // deve acompanhar a transition-duration de .acesso-conteudo-formulario
            elemento.addEventListener('transitionend', resolver, { once: true });
            setTimeout(resolver, duracaoMs + 50);
        });
    }
});
