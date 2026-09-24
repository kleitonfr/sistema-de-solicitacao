/**
 * Alternador do painel ESIC (deslize entre Sistema 651 e ESIC).
 *
 * Ao clicar em "Acesse o ESIC por aqui" / "Acesse o Portal 156", alterna a
 * classe .modo-esic no elemento .acesso-split (dispara a animação CSS de
 * deslize dos painéis — ver acesso.css). Em ambos os sentidos, busca o
 * fragmento de Entrar do lado de destino via AJAX e o injeta no painel
 * correspondente, garantindo que tanto o Sistema 651 quanto o ESIC sempre
 * reapareçam no estado "Entrar" (nunca com "Cadastrar" preso de uma troca
 * anterior no alternador Entrar/Cadastrar daquele lado) — e resincroniza
 * o próprio alternador (opção ativa + indicador) de cada lado.
 *
 * Reaproveita buscarFragmento e atualizarEstadoAtivo de
 * alternador-acesso.js (DRY) — mesma lógica de requisição e de estado
 * visual usada pelo alternador Entrar/Cadastrar.
 */
import { buscarFragmento, atualizarEstadoAtivo } from './alternador-acesso';

document.addEventListener('DOMContentLoaded', () => {
    const botaoEsic = document.getElementById('acesso-toggle-esic');
    const split = document.querySelector('.acesso-split');
    const conteudoEsic = document.getElementById('esic-conteudo-formulario');
    const conteudoPortal = document.getElementById('acesso-conteudo-formulario');

    if (!botaoEsic || !split || !conteudoEsic || !conteudoPortal) {
        return;
    }

    const alternadorEsic = conteudoEsic.closest('.acesso-split__esic-conteudo')?.querySelector('.acesso-alternador');
    const alternadorPortal = conteudoPortal.closest('.acesso-split__form-inner')?.querySelector('.acesso-alternador');

    let trocaEmAndamento = false;

    botaoEsic.addEventListener('click', async (evento) => {
        evento.preventDefault();

        if (trocaEmAndamento) {
            return;
        }

        let paginaUrl = botaoEsic.getAttribute('href');
        const entrandoNoEsic = !split.classList.contains('modo-esic');

        trocaEmAndamento = true;

        try {
            if (entrandoNoEsic) {
                const fragmentoUrl = botaoEsic.dataset.fragmentoEsicEntrar;
                const html = await buscarFragmento(fragmentoUrl);
                conteudoEsic.innerHTML = html;
                paginaUrl = botaoEsic.dataset.urlEsic;

                const opcaoEntrarEsic = alternadorEsic?.querySelector('.acesso-alternador__opcao');
                if (alternadorEsic && opcaoEntrarEsic) {
                    atualizarEstadoAtivo(alternadorEsic, opcaoEntrarEsic);
                }
            }
            else {
                const fragmentoUrl = botaoEsic.dataset.fragmentoPortalEntrar;
                const html = await buscarFragmento(fragmentoUrl);
                conteudoPortal.innerHTML = html;
                paginaUrl = botaoEsic.dataset.urlPortal;

                const opcaoEntrarPortal = alternadorPortal?.querySelector('.acesso-alternador__opcao');
                if (alternadorPortal && opcaoEntrarPortal) {
                    atualizarEstadoAtivo(alternadorPortal, opcaoEntrarPortal);
                }
            }

            split.classList.toggle('modo-esic');
            history.pushState({}, '', paginaUrl);
        } catch (erro) {
            // Sem integração com backend de log neste projeto (frontend puro);
            // navega normalmente como reserva, garantindo que o usuário
            // sempre consiga acessar o ESIC mesmo se o AJAX falhar.
            window.location.href = paginaUrl;
        } finally {
            trocaEmAndamento = false;
        }
    });
});
