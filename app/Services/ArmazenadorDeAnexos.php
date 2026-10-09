<?php

namespace App\Services;

use App\Exceptions\AnexoIndisponivel;
use App\Exceptions\FalhaAoArmazenarAnexo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ArmazenadorDeAnexos
{
    private const DISCO_DOS_ANEXOS = 'local';

    /**
     * O disco 'local' está com throw=false: quando a gravação falha (disco
     * cheio, sem permissão), store() devolve false em vez de lançar exceção.
     * Sem esta verificação, a solicitação seria salva com um anexo inexistente.
     */
    public function armazenar(UploadedFile $arquivo, string $diretorio): string
    {
        $caminho = $arquivo->store($diretorio, self::DISCO_DOS_ANEXOS);

        if ($caminho === false || $caminho === '') {
            throw FalhaAoArmazenarAnexo::noDiretorio($diretorio);
        }

        return $caminho;
    }

    /**
     * Sempre como download (attachment) e com nosniff: o arquivo é conteúdo
     * enviado por um cidadão e o navegador nunca deve exibi-lo como página.
     *
     * @throws AnexoIndisponivel
     */
    public function baixar(string $caminho, string $nomeOriginal): StreamedResponse
    {
        $disco = Storage::disk(self::DISCO_DOS_ANEXOS);

        if (! $disco->exists($caminho)) {
            throw AnexoIndisponivel::ausenteDoArmazenamento();
        }

        return $disco->download($caminho, $nomeOriginal, ['X-Content-Type-Options' => 'nosniff']);
    }

    /**
     * Usado na compensação de uma falha: um erro ao apagar não pode mascarar
     * o erro original, então só é registrado.
     *
     * @param  list<string>  $caminhos
     */
    public function apagar(array $caminhos): void
    {
        foreach ($caminhos as $caminho) {
            try {
                $apagado = Storage::disk(self::DISCO_DOS_ANEXOS)->delete($caminho);
            } catch (Throwable) {
                $apagado = false;
            }

            if (! $apagado) {
                Log::warning('anexo_orfao_nao_removido', ['caminho' => $caminho]);
            }
        }
    }
}
