/**
 * Comportamento da área de anexos (ver components/dropzone-anexos.blade.php).
 *
 * O <input type="file"> real cobre a área; aqui só se mantém a lista de
 * arquivos escolhidos (clique ou arrastar), se exibe essa lista e se
 * devolve a lista ao input via DataTransfer, para o envio do formulário
 * refletir o que está na tela. data-maximo-arquivos igual a 1 substitui o
 * arquivo anterior; acima disso, acumula até o limite.
 */
const BYTES_POR_KB = 1024;
const BYTES_POR_MB = 1024 * 1024;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dropzone]').forEach(iniciarDropzone);
});

function iniciarDropzone(area) {
    const input = area.querySelector('.dropzone__input');
    const vazio = area.querySelector('[data-dropzone-vazio]');
    const lista = area.querySelector('[data-dropzone-lista]');
    const aviso = area.querySelector('[data-dropzone-aviso]');
    const maximoDeArquivos = Number(area.dataset.maximoArquivos);

    let arquivos = [];

    input.addEventListener('change', () => adicionarArquivos(Array.from(input.files)));

    area.addEventListener('dragover', (evento) => {
        evento.preventDefault();
        area.classList.add('dropzone--arrastando');
    });

    area.addEventListener('dragleave', () => area.classList.remove('dropzone--arrastando'));

    area.addEventListener('drop', (evento) => {
        evento.preventDefault();
        area.classList.remove('dropzone--arrastando');
        adicionarArquivos(Array.from(evento.dataTransfer?.files ?? []));
    });

    lista.addEventListener('click', (evento) => {
        const botaoRemover = evento.target.closest('[data-remover]');

        if (!botaoRemover) {
            return;
        }

        arquivos.splice(Number(botaoRemover.dataset.remover), 1);
        avisar('');
        atualizar();
    });

    function adicionarArquivos(novosArquivos) {
        if (novosArquivos.length === 0) {
            return;
        }

        const combinados = maximoDeArquivos === 1
            ? novosArquivos
            : [...arquivos, ...novosArquivos.filter((novo) => !arquivos.some((atual) => ehMesmoArquivo(atual, novo)))];

        arquivos = combinados.slice(0, maximoDeArquivos);

        avisar(combinados.length > maximoDeArquivos ? mensagemDeLimite() : '');
        atualizar();
    }

    function atualizar() {
        sincronizarInput();
        vazio.hidden = arquivos.length > 0;
        lista.hidden = arquivos.length === 0;
        lista.replaceChildren(...arquivos.map(criarItem));
    }

    function sincronizarInput() {
        const transferencia = new DataTransfer();
        arquivos.forEach((arquivo) => transferencia.items.add(arquivo));
        input.files = transferencia.files;
    }

    function avisar(mensagem) {
        aviso.textContent = mensagem;
        aviso.hidden = mensagem === '';
    }

    function mensagemDeLimite() {
        return maximoDeArquivos === 1
            ? 'Apenas um arquivo pode ser anexado.'
            : `Você pode anexar no máximo ${maximoDeArquivos} arquivos.`;
    }
}

function criarItem(arquivo, indice) {
    const item = document.createElement('li');
    item.className = 'dropzone__item';

    const icone = document.createElement('i');
    icone.className = `${iconeDoArquivo(arquivo)} dropzone__item-icone`;
    icone.setAttribute('aria-hidden', 'true');

    const nome = document.createElement('span');
    nome.className = 'dropzone__item-nome';
    nome.textContent = arquivo.name;

    const tamanho = document.createElement('span');
    tamanho.className = 'dropzone__item-tamanho';
    tamanho.textContent = formatarTamanho(arquivo.size);

    const botaoRemover = document.createElement('button');
    botaoRemover.type = 'button';
    botaoRemover.className = 'dropzone__remover';
    botaoRemover.dataset.remover = String(indice);
    botaoRemover.setAttribute('aria-label', `Remover ${arquivo.name}`);
    botaoRemover.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';

    item.append(icone, nome, tamanho, botaoRemover);

    return item;
}

function ehMesmoArquivo(a, b) {
    return a.name === b.name && a.size === b.size && a.lastModified === b.lastModified;
}

function iconeDoArquivo(arquivo) {
    if (arquivo.type.startsWith('image/')) {
        return 'fa-solid fa-image';
    }

    if (arquivo.type.startsWith('video/')) {
        return 'fa-solid fa-video';
    }

    if (arquivo.type === 'application/pdf') {
        return 'fa-solid fa-file-pdf';
    }

    return 'fa-solid fa-file-lines';
}

function formatarTamanho(bytes) {
    if (bytes < BYTES_POR_MB) {
        return `${Math.max(1, Math.round(bytes / BYTES_POR_KB))} KB`;
    }

    return `${(bytes / BYTES_POR_MB).toFixed(1).replace('.', ',')} MB`;
}
