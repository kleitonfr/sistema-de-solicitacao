/**
 * Alterna a exibição dos campos de Pessoa Física / Pessoa Jurídica no
 * formulário de Cadastro do Sistema 651, conforme o radio "Tipo de
 * Pessoa" selecionado.
 *
 * Puramente de exibição (nenhuma regra de negócio aqui) — os campos do
 * tipo não selecionado ficam com o atributo `hidden`, que também os
 * exclui da submissão do formulário (Firefox/Chrome/Safari não enviam
 * valores de campos hidden).
 */
document.addEventListener('DOMContentLoaded', () => {
    const radiosTipoPessoa = document.querySelectorAll('[data-alterna-tipo-pessoa]');

    if (!radiosTipoPessoa.length) {
        return;
    }

    function aplicarTipoPessoa(tipoSelecionado) {
        document.querySelectorAll('[data-campos-tipo-pessoa]').forEach((grupoCampos) => {
            const ehDoTipoSelecionado = grupoCampos.dataset.camposTipoPessoa === tipoSelecionado;
            grupoCampos.hidden = !ehDoTipoSelecionado;
        });
    }

    radiosTipoPessoa.forEach((radio) => {
        radio.addEventListener('change', () => {
            if (radio.checked) {
                aplicarTipoPessoa(radio.dataset.alternaTipoPessoa);
            }
        });
    });

    const radioMarcado = document.querySelector('[data-alterna-tipo-pessoa]:checked');
    if (radioMarcado) {
        aplicarTipoPessoa(radioMarcado.dataset.alternaTipoPessoa);
    }
});
