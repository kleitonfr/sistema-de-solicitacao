<?php

namespace App\Services;

use App\Exceptions\SolicitacaoNaoPodeSerRegistrada;
use App\Logging\ContextoDaFalha;
use App\Logging\RegistroDeSeguranca;
use App\Models\ManifestacaoOuvidoria;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OuvidoriaService
{
    private const PREFIXO_DO_PROTOCOLO = 'OUV';

    private const DIRETORIO_DOS_ANEXOS = 'manifestacoes-ouvidoria';

    public function __construct(
        private readonly GeradorDeProtocolo $geradorDeProtocolo,
        private readonly ArmazenadorDeAnexos $armazenadorDeAnexos,
        private readonly RegistroDeSeguranca $registroDeSeguranca,
    ) {
    }

    /**
     * @param  list<UploadedFile>  $anexos
     *
     * @throws SolicitacaoNaoPodeSerRegistrada
     */
    public function registrarManifestacao(User $usuario, string $tipo, array $dados, array $anexos): ManifestacaoOuvidoria
    {
        $caminhosGravados = [];

        DB::beginTransaction();

        try {
            $manifestacao = ManifestacaoOuvidoria::create([
                ...Arr::only($dados, ['orgao_ou_setor', 'descricao', 'data_do_fato', 'local_do_fato', 'servidor_ou_setor']),
                'user_id' => $usuario->id,
                'tipo' => $tipo,
                'protocolo' => $this->geradorDeProtocolo->gerar(self::PREFIXO_DO_PROTOCOLO, ManifestacaoOuvidoria::class),
            ]);

            foreach ($anexos as $anexo) {
                $caminho = $this->armazenadorDeAnexos->armazenar($anexo, self::DIRETORIO_DOS_ANEXOS."/{$manifestacao->protocolo}");
                $caminhosGravados[] = $caminho;

                $manifestacao->anexos()->create([
                    'nome_original' => $anexo->getClientOriginalName(),
                    'caminho' => $caminho,
                ]);
            }

            DB::commit();
        } catch (Throwable $erro) {
            DB::rollBack();
            $this->armazenadorDeAnexos->apagar($caminhosGravados);

            Log::error('manifestacao_ouvidoria_nao_registrada', [
                ...ContextoDaFalha::deExcecao($erro),
                'usuario_id' => $usuario->id,
                'tipo' => $tipo,
                'quantidade_de_anexos' => count($anexos),
            ]);

            throw SolicitacaoNaoPodeSerRegistrada::por($erro);
        }

        Log::info('manifestacao_ouvidoria_registrada', [
            'usuario_id' => $usuario->id,
            'protocolo' => $manifestacao->protocolo,
            'tipo' => $tipo,
            'quantidade_de_anexos' => count($anexos),
        ]);

        return $manifestacao;
    }

    public function buscarManifestacaoDoUsuario(User $usuario, string $protocolo): ManifestacaoOuvidoria
    {
        try {
            return $usuario->manifestacoesOuvidoria()
                ->with('anexos')
                ->where('protocolo', $protocolo)
                ->firstOrFail();
        } catch (ModelNotFoundException $erro) {
            $this->registroDeSeguranca->aviso('manifestacao_ouvidoria_nao_encontrada_para_o_usuario', [
                'usuario_id' => $usuario->id,
                'protocolo' => $protocolo,
            ]);

            throw $erro;
        }
    }
}
