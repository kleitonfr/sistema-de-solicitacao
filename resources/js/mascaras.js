/**
 * Aplica máscara visual aos campos marcados com data-mask (cpf, cnpj,
 * telefone, cep) ao digitar. Puramente de apresentação — a validação de
 * fato (formato, dígitos verificadores) acontece no FormRequest/Rule
 * correspondente no backend.
 *
 * aplicarMascaras(raiz) fica em window porque os formulários de cadastro
 * são trocados via fetch + innerHTML (ver alternador-tipo-pessoa.js e
 * alternador-acesso.js): os campos novos não existiam no DOMContentLoaded
 * original, então cada troca precisa religar as máscaras nos campos recém
 * inseridos, passando o novo contêiner como raiz.
 */
const FORMATADORES = {
    cpf: (valor) => valor
        .slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2'),

    cnpj: (valor) => valor
        .slice(0, 14)
        .replace(/(\d{2})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2')
        .replace(/(\d{4})(\d{1,2})$/, '$1-$2'),

    telefone: (valor) => {
        valor = valor.slice(0, 9);
        return valor.length > 4
            ? valor.replace(/(\d{4,5})(\d{1,4})$/, '$1-$2')
            : valor;
    },

    cep: (valor) => valor
        .slice(0, 8)
        .replace(/(\d{5})(\d{1,3})$/, '$1-$2'),
};

function aplicarMascaras(raiz = document) {
    raiz.querySelectorAll('[data-mask]').forEach((campo) => {
        if (campo.dataset.mascaraAplicada) {
            return;
        }

        const formatar = FORMATADORES[campo.dataset.mask];

        if (!formatar) {
            return;
        }

        campo.addEventListener('input', () => {
            const apenasDigitos = campo.value.replace(/\D/g, '');
            campo.value = formatar(apenasDigitos);
        });

        campo.dataset.mascaraAplicada = 'true';
    });
}

window.aplicarMascaras = aplicarMascaras;

document.addEventListener('DOMContentLoaded', () => aplicarMascaras());
