<?php

namespace App\Services;

use App\Exceptions\SolicitacaoNaoPodeSerRegistrada;
use App\Logging\ContextoDaFalha;
use App\Logging\RegistroDeSeguranca;
use App\Models\SolicitacaoEsic;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EsicService
{
    private const PREFIXO_DO_PROTOCOLO = 'ESIC';

    private const DIRETORIO_DOS_ANEXOS = 'solicitacoes-esic';

    public function __construct(
        private readonly GeradorDeProtocolo $geradorDeProtocolo,
        private readonly ArmazenadorDeAnexos $armazenadorDeAnexos,
        private readonly RegistroDeSeguranca $registroDeSeguranca,
        private readonly StatusDaSolicitacaoService $statusDaSolicitacaoService,
    ) {
    }

    /**
     * @throws SolicitacaoNaoPodeSerRegistrada
     */
    public function registrarSolicitacao(User $usuario, array $dados, ?UploadedFile $anexo): SolicitacaoEsic
    {
        $caminhoDoAnexo = null;

        DB::beginTransaction();

        try {
            if ($anexo !== null) {
                $caminhoDoAnexo = $this->armazenadorDeAnexos->armazenar($anexo, self::DIRETORIO_DOS_ANEXOS);
            }

            $solicitacao = SolicitacaoEsic::create([
                'user_id' => $usuario->id,
                'protocolo' => $this->geradorDeProtocolo->gerar(self::PREFIXO_DO_PROTOCOLO, SolicitacaoEsic::class),
                'assunto' => $dados['assunto'],
                'descricao' => $dados['descricao'],
                'anexo_nome_original' => $anexo?->getClientOriginalName(),
                'anexo_caminho' => $caminhoDoAnexo,
            ]);

            $this->statusDaSolicitacaoService->registrarRecebimento($solicitacao);

            DB::commit();
        } catch (Throwable $erro) {
            DB::rollBack();
            $this->armazenadorDeAnexos->apagar($caminhoDoAnexo === null ? [] : [$caminhoDoAnexo]);

            Log::error('solicitacao_esic_nao_registrada', [
                ...ContextoDaFalha::deExcecao($erro),
                'usuario_id' => $usuario->id,
                'possui_anexo' => $anexo !== null,
            ]);

            throw SolicitacaoNaoPodeSerRegistrada::por($erro);
        }

        Log::info('solicitacao_esic_registrada', [
            'usuario_id' => $usuario->id,
            'protocolo' => $solicitacao->protocolo,
            'possui_anexo' => $anexo !== null,
        ]);

        return $solicitacao;
    }

    public function buscarSolicitacaoDoUsuario(User $usuario, string $protocolo): SolicitacaoEsic
    {
        try {
            return $usuario->solicitacoesEsic()->where('protocolo', $protocolo)->firstOrFail();
        } catch (ModelNotFoundException $erro) {
            $this->registroDeSeguranca->aviso('solicitacao_esic_nao_encontrada_para_o_usuario', [
                'usuario_id' => $usuario->id,
                'protocolo' => $protocolo,
            ]);

            throw $erro;
        }
    }
}
