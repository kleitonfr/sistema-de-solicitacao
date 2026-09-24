/**
 * Alternador do painel ESIC (deslize entre Sistema 651 e ESIC).
 *
 * Ao clicar em "Acesse o ESIC por aqui" / "Acesse o Portal 156", alterna a
 * classe .modo-esic no elemento .acesso-split (dispara a animação CSS de
 * deslize dos painéis — ver acesso.css) e, ao entrar no modo ESIC, busca
 * o fragmento do formulário de Entrar do ESIC via AJAX, populando o painel
 * que acabou de aparecer. Ao sair do modo ESIC, apenas navega de volta
 * para o Sistema 651 (já visível por trás, conforme decisão de produto).
 *
 * Reaproveita buscarFragmento de alternador-acesso.js (DRY) — mesma lógica
 * de requisição usada pelo alternador Entrar/Cadastrar.
 */
import { buscarFragmento } from './alternador-acesso';

document.addEventListener('DOMContentLoaded', () => {
    const botaoEsic = document.getElementById('acesso-toggle-esic');
    const split = document.querySelector('.acesso-split');
    const conteudoEsic = document.getElementById('esic-conteudo-formulario');

    if (!botaoEsic || !split || !conteudoEsic) {
        return;
    }

    let trocaEmAndamento = false;

    botaoEsic.addEventListener('click', async (evento) => {
        evento.preventDefault();

        if (trocaEmAndamento) {
            return;
        }

        const paginaUrl = botaoEsic.getAttribute('href');
        const entrandoNoEsic = !split.classList.contains('modo-esic');

        trocaEmAndamento = true;

        try {
            if (entrandoNoEsic) {
                const fragmentoUrl = botaoEsic.dataset.fragmentoEsicEntrar;
                const html = await buscarFragmento(fragmentoUrl);
                conteudoEsic.innerHTML = html;
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
