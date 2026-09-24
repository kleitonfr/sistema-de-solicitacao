/**
 * Alternador Entrar/Cadastrar — Sistema 651 e ESIC.
 *
 * Ao clicar em "Entrar" ou "Cadastrar" (em qualquer um dos alternadores da
 * página — o do Sistema 651 e o do painel ESIC, que reaproveita a mesma
 * marcação), busca o fragmento HTML do formulário correspondente via
 * requisição AJAX (fetch) e substitui o conteúdo do painel sem recarregar
 * a página, com uma transição de fade suave. A URL do navegador é
 * atualizada via history.pushState, preservando o comportamento do botão
 * voltar/avançar e a possibilidade de compartilhar o link direto de cada
 * formulário.
 *
 * Usa delegação de evento (um único listener em document) em vez de
 * selecionar um alternador específico — necessário porque a página tem
 * DOIS alternadores com a mesma marcação (.acesso-alternador): um para o
 * Sistema 651 (dentro de #acesso-conteudo-formulario) e outro para o ESIC
 * (dentro de #esic-conteudo-formulario). Um querySelector pegaria só o
 * primeiro; a delegação funciona para qualquer um dos dois, inclusive se
 * mais painéis desse tipo forem adicionados no futuro.
 *
 * Rotas consumidas (ver routes/web.php): fragmentos.entrar,
 * fragmentos.cadastro, fragmentos.esic.entrar e fragmentos.esic.cadastro —
 * servidas por FragmentoAcessoController e FragmentoEsicController,
 * endpoints dedicados que devolvem só o HTML do formulário, sem o layout
 * ao redor.
 */
let trocaEmAndamento = false;

document.addEventListener('click', (evento) => {
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

    const alternador = opcaoClicada.closest('.acesso-alternador');
    const conteudo = alternador?.parentElement?.querySelector('.acesso-conteudo-formulario');
    const indicador = alternador?.querySelector('.acesso-alternador__indicador');

    const fragmentoUrl = opcaoClicada.dataset.fragmentoUrl;
    const paginaUrl = opcaoClicada.getAttribute('href');

    if (!alternador || !conteudo || !indicador || !fragmentoUrl || !paginaUrl) {
        return;
    }

    trocaEmAndamento = true;

    try {
        const html = await buscarFragmento(fragmentoUrl);

        conteudo.classList.add('esta-trocando');
        await aguardarFimDaTransicao(conteudo);

        conteudo.innerHTML = html;
        conteudo.classList.remove('esta-trocando');

        atualizarEstadoAtivo(alternador, opcaoClicada);
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

export async function buscarFragmento(fragmentoUrl) {
    const resposta = await fetch(fragmentoUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    if (!resposta.ok) {
        throw new Error('Falha ao carregar o formulário.');
    }

    return resposta.text();
}

function atualizarEstadoAtivo(alternador, opcaoClicada) {
    const opcoes = alternador.querySelectorAll('.acesso-alternador__opcao');
    const indicador = alternador.querySelector('.acesso-alternador__indicador');

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
