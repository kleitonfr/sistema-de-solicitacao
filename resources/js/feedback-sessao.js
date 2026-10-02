/**
 * Faz os alertas de sucesso (marcados com data-fecha-automatico pelo
 * componente <x-cadastro-alerta>) desaparecerem sozinhos após um tempo,
 * sem exigir ação do usuário. Alertas de erro permanecem visíveis até o
 * usuário corrigir o formulário e reenviar.
 */
const TEMPO_VISIVEL_MS = 5000;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-fecha-automatico]').forEach((alerta) => {
        setTimeout(() => fecharAlerta(alerta), TEMPO_VISIVEL_MS);
    });
});

function fecharAlerta(alerta) {
    alerta.classList.add('cadastro-alert--saindo');
    alerta.addEventListener('transitionend', () => alerta.remove(), { once: true });
}
