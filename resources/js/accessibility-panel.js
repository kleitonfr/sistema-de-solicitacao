const btnAbrir = document.getElementById('btn-abrir-acessibilidade');
const painel = document.getElementById('painel-acessibilidade');

if (btnAbrir && painel) {

    painel.style.display = 'flex';
    painel.style.transformOrigin = 'left';
    painel.style.transition = 'transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease, visibility 0s linear 0.25s';
    painel.style.transform = 'scale(0.6) translateX(16px)';
    painel.style.opacity = '0';
    painel.style.visibility = 'hidden';
    painel.style.pointerEvents = 'none';

    let aberto = false;

    function abrirPainel() {
        aberto = true;
        btnAbrir.setAttribute('aria-expanded', 'true');

        painel.style.transition = 'transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease, visibility 0s linear 0s';
        painel.style.visibility = 'visible';
        painel.style.pointerEvents = 'auto';

        requestAnimationFrame(() => requestAnimationFrame(() => {
            painel.style.transform = 'scale(1) translateX(0)';
            painel.style.opacity = '1';
        }));
    }

    function fecharPainel() {
        aberto = false;
        btnAbrir.setAttribute('aria-expanded', 'false');

        painel.style.transition = 'transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.22s ease, visibility 0s linear 0.25s';
        painel.style.transform = 'scale(0.6) translateX(16px)';
        painel.style.opacity = '0';
        painel.style.visibility = 'hidden';
        painel.style.pointerEvents = 'none';
    }

    // Botão abre/fecha ao clicar — impede que o clique chegue ao document
    btnAbrir.addEventListener('click', (e) => {
        e.stopPropagation();
        if (aberto) {
            fecharPainel();
        } else {
            abrirPainel();
        }
    });

    // Cliques dentro do painel não fecham o painel
    painel.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Clique fora do widget fecha o painel
    document.addEventListener('click', () => {
        if (aberto) {
            fecharPainel();
        }
    });
}
