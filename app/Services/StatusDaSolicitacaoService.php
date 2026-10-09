<?php

namespace App\Services;

use App\Exceptions\TransicaoDeStatusInvalida;
use App\Logging\ContextoDaFalha;
use App\Models\MovimentacaoDaSolicitacao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class StatusDaSolicitacaoService
{
    private const RESPONSAVEL_PELO_RECEBIMENTO = 'sistema';

    private const MENSAGEM_DO_RECEBIMENTO = 'Recebemos sua solicitação.';

    /**
     * Não abre transação: roda dentro da transação do service que acabou de
     * criar a solicitação, para que as duas coisas sejam gravadas juntas ou
     * nenhuma delas.
     */
    public function registrarRecebimento(Model $solicitacao): void
    {
        $solicitacao->movimentacoes()->create([
            'status_anterior' => null,
            'status_novo' => MovimentacaoDaSolicitacao::STATUS_INICIAL,
            'mensagem' => self::MENSAGEM_DO_RECEBIMENTO,
            'registrado_por' => self::RESPONSAVEL_PELO_RECEBIMENTO,
        ]);
    }

    /**
     * A solicitação é relida com bloqueio dentro da transação: duas mudanças
     * simultâneas não conseguem partir do mesmo status.
     *
     * @throws TransicaoDeStatusInvalida
     */
    public function alterarStatus(
        Model $solicitacaoInformada,
        string $novoStatus,
        ?string $mensagem,
        string $registradoPor,
    ): MovimentacaoDaSolicitacao {
        $statusAnterior = null;

        DB::beginTransaction();

        try {
            $solicitacao = $solicitacaoInformada->newQuery()
                ->lockForUpdate()
                ->findOrFail($solicitacaoInformada->getKey());

            $statusAnterior = $solicitacao->status;

            if (! MovimentacaoDaSolicitacao::transicaoEhPermitida($statusAnterior, $novoStatus)) {
                throw TransicaoDeStatusInvalida::entre($statusAnterior, $novoStatus);
            }

            $solicitacao->forceFill(['status' => $novoStatus])->save();

            $movimentacao = $solicitacao->movimentacoes()->create([
                'status_anterior' => $statusAnterior,
                'status_novo' => $novoStatus,
                'mensagem' => $mensagem,
                'registrado_por' => $registradoPor,
            ]);

            DB::commit();
        } catch (TransicaoDeStatusInvalida $erro) {
            DB::rollBack();

            Log::warning('transicao_de_status_recusada', [
                'tipo_da_solicitacao' => $solicitacaoInformada->getMorphClass(),
                'protocolo' => $solicitacaoInformada->protocolo,
                'status_atual' => $statusAnterior,
                'status_pedido' => $novoStatus,
            ]);

            throw $erro;
        } catch (Throwable $erro) {
            DB::rollBack();

            Log::error('status_da_solicitacao_nao_alterado', [
                ...ContextoDaFalha::deExcecao($erro),
                'tipo_da_solicitacao' => $solicitacaoInformada->getMorphClass(),
                'protocolo' => $solicitacaoInformada->protocolo,
                'status_pedido' => $novoStatus,
            ]);

            throw $erro;
        }

        $solicitacaoInformada->setAttribute('status', $novoStatus)->syncOriginalAttribute('status');

        Log::info('status_da_solicitacao_alterado', [
            'tipo_da_solicitacao' => $solicitacaoInformada->getMorphClass(),
            'protocolo' => $solicitacaoInformada->protocolo,
            'status_anterior' => $statusAnterior,
            'status_novo' => $novoStatus,
            'registrado_por' => $registradoPor,
        ]);

        return $movimentacao;
    }
}
