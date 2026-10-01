/**
 * Área de arquivo anexo (arrastar e soltar) do formulário de Registrar
 * Pedido do e-SIC (ver resources/views/servicos/esic/solicitacao/
 * criar.blade.php).
 *
 * O <input type="file"> real fica invisível, cobrindo toda a área da
 * dropzone (ver .esic-solicitacao-dropzone__input em
 * pages/esic-solicitacao.css) — isso já entrega clique-para-selecionar e
 * teclado de graça, sem reimplementar nada. Este script cuida só de:
 * 1) alternar a aparência durante o arrastar (dragover/dragleave);
 * 2) mostrar o nome do arquivo escolhido (clique ou drop) no lugar do
 *    texto de instrução;
 * 3) permitir remover o arquivo selecionado sem recarregar a página.
 */
document.addEventListener('DOMContentLoaded', () => {
    const area = document.getElementById('esic-anexo-area');

    if (!area) {
        return;
    }

    const input = area.querySelector('.esic-solicitacao-dropzone__input');
    const estadoVazio = document.getElementById('esic-anexo-estado-vazio');
    const estadoSelecionado = document.getElementById('esic-anexo-estado-selecionado');
    const nomeArquivo = document.getElementById('esic-anexo-nome');
    const botaoRemover = document.getElementById('esic-anexo-remover');

    input.addEventListener('change', () => {
        atualizarArquivoExibido();
    });

    area.addEventListener('dragover', (evento) => {
        evento.preventDefault();
        area.classList.add('esic-solicitacao-dropzone--arrastando-sobre');
    });

    area.addEventListener('dragleave', () => {
        area.classList.remove('esic-solicitacao-dropzone--arrastando-sobre');
    });

    area.addEventListener('drop', (evento) => {
        evento.preventDefault();
        area.classList.remove('esic-solicitacao-dropzone--arrastando-sobre');

        const arquivos = evento.dataTransfer?.files;

        if (arquivos && arquivos.length > 0) {
            input.files = arquivos;
            atualizarArquivoExibido();
        }
    });

    botaoRemover.addEventListener('click', (evento) => {
        // Impede que o clique no "x" borbulhe para a dropzone e reabra o
        // seletor de arquivos por cima do estado "vazio" que está prestes
        // a reaparecer.
        evento.stopPropagation();
        input.value = '';
        atualizarArquivoExibido();
    });

    function atualizarArquivoExibido() {
        const arquivo = input.files?.[0];

        if (!arquivo) {
            estadoVazio.hidden = false;
            estadoSelecionado.hidden = true;
            return;
        }

        nomeArquivo.textContent = arquivo.name;
        estadoVazio.hidden = true;
        estadoSelecionado.hidden = false;
    }
});
