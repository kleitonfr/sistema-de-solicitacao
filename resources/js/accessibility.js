const TAMANHOS = [14, 16, 18, 20, 22, 24];
let indiceAtual = 1;

const contrasteSalvo = localStorage.getItem('alto-contraste');

if (contrasteSalvo) {
    document.body.classList.toggle('alto-contraste', contrasteSalvo === 'true');
}

const tamanhoSalvo = localStorage.getItem('font-size');

if (tamanhoSalvo) {
    indiceAtual = TAMANHOS.indexOf(parseInt(tamanhoSalvo, 10));
    if (indiceAtual === -1) indiceAtual = 1;
}

// Tamanho de fonte
const btnAumentar = document.getElementById('btn-aumentar');
const btnDiminuir = document.getElementById('btn-diminuir');

if (btnAumentar && btnDiminuir) {

    function aplicarFonte() {
        indiceAtual = Math.max(0, Math.min(indiceAtual, TAMANHOS.length - 1));

        // Altera o font-size do <html> para escalar tudo via rem.
        document.documentElement.style.fontSize = TAMANHOS[indiceAtual] + 'px';

        btnAumentar.disabled = indiceAtual >= TAMANHOS.length - 1;
        btnDiminuir.disabled = indiceAtual <= 0;

        localStorage.setItem('font-size', TAMANHOS[indiceAtual]);
    }

    aplicarFonte();

    btnAumentar.addEventListener('click', () => {
        indiceAtual++;
        aplicarFonte();
    });

    btnDiminuir.addEventListener('click', () => {
        indiceAtual--;
        aplicarFonte();
    });
}

// Alto contraste
const btnContraste = document.getElementById('btn-contraste');

if (btnContraste) {
    btnContraste.addEventListener('click', () => {
        document.body.classList.toggle('alto-contraste');
        const ativo = document.body.classList.contains('alto-contraste');
        localStorage.setItem('alto-contraste', ativo);
        btnContraste.setAttribute('aria-pressed', ativo);
    });
}
