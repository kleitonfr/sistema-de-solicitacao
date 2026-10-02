/**
 * Alternador Física/Jurídica do formulário de Cadastro.
 *
 * Ao clicar no radio "Física" ou "Jurídica", busca o formulário completo
 * correspondente via requisição AJAX (fetch) e substitui inteiramente o
 * conteúdo de #acesso-conteudo-formulario — não é uma alternância de campos
 * escondidos dentro do mesmo <form>, são dois formulários completos e
 * independentes (ver
 * resources/views/portal/parciais/formulario-cadastro-fisica.blade.php e
 * -juridica.blade.php), cada um com sua própria rota de envio.
 *
 * Física é o padrão: a página de cadastro sempre carrega com esse
 * formulário; o de Jurídica só existe no DOM depois que o usuário troca.
 *
 * As URLs dos fragmentos vêm de data-url-cadastro-fisica e
 * data-url-cadastro-juridica no próprio #acesso-conteudo-formulario
 * (layouts/acesso.blade.php). Esse contêiner nunca é substituído — só o
 * innerHTML dele —, então as URLs existem em qualquer tela, inclusive
 * quando a página foi carregada em /entrar e o usuário chegou ao cadastro
 * pelo alternador Entrar/Cadastrar.
 *
 * Rotas consumidas (ver routes/web.php): fragmentos.cadastro.fisica e
 * fragmentos.cadastro.juridica — servidas por
 * FragmentoAcessoController::cadastroFisica/cadastroJuridica.
 */
document.addEventListener('DOMContentLoaded', () => {
    const conteudo = document.getElementById('acesso-conteudo-formulario');

    if (!conteudo) {
        return;
    }

    const fragmentoUrlPorTipo = {
        fisica: conteudo.dataset.urlCadastroFisica,
        juridica: conteudo.dataset.urlCadastroJuridica,
    };

    let trocaEmAndamento = false;

    conteudo.addEventListener('change', (evento) => {
        const radioClicado = evento.target.closest('[data-alterna-tipo-pessoa]');

        if (!radioClicado || !radioClicado.checked) {
            return;
        }

        trocarTipoPessoa(radioClicado.dataset.alternaTipoPessoa);
    });

    async function trocarTipoPessoa(tipoPessoa) {
        if (trocaEmAndamento) {
            return;
        }

        const fragmentoUrl = fragmentoUrlPorTipo[tipoPessoa];

        if (!fragmentoUrl) {
            return;
        }

        trocaEmAndamento = true;

        try {
            const resposta = await fetch(fragmentoUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!resposta.ok) {
                throw new Error('Falha ao carregar o formulário.');
            }

            const html = await resposta.text();

            conteudo.classList.add('esta-trocando');
            await aguardarFimDaTransicao(conteudo);

            conteudo.innerHTML = html;
            conteudo.classList.remove('esta-trocando');
            window.aplicarMascaras(conteudo);
        } catch (erro) {
            // Sem integração com backend de log neste projeto (frontend puro);
            // sem reserva de navegação aqui porque a troca Física/Jurídica não
            // tem uma URL própria — o usuário permanece no formulário atual
            // se o AJAX falhar.
        } finally {
            trocaEmAndamento = false;
        }
    }

    function aguardarFimDaTransicao(elemento) {
        return new Promise((resolver) => {
            const duracaoMs = 180; // deve acompanhar a transition-duration de .acesso-conteudo-formulario
            elemento.addEventListener('transitionend', resolver, { once: true });
            setTimeout(resolver, duracaoMs + 50);
        });
    }
});
