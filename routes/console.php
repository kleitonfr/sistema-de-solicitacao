<?php

use App\Exceptions\TransicaoDeStatusInvalida;
use App\Models\MovimentacaoDaSolicitacao;
use App\Services\MinhasSolicitacoesService;
use App\Services\StatusDaSolicitacaoService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Ferramenta de desenvolvimento: o projeto é só frontend e ainda não existe
 * sistema interno que altere o status. Usa as mesmas regras do
 * StatusDaSolicitacaoService, que a futura origem real também deve chamar.
 */
Artisan::command(
    'solicitacao:avancar-status {protocolo : Protocolo (ex.: OUV-2026-000001)} {status : recebida, em_analise, encaminhada, em_atendimento ou concluida} {mensagem? : Texto mostrado ao cidadão na linha do tempo}',
    function (StatusDaSolicitacaoService $statusDaSolicitacaoService): int {
        if (app()->isProduction()) {
            $this->error('Este comando não pode ser usado em produção.');

            return 1;
        }

        $novoStatus = (string) $this->argument('status');

        if (! array_key_exists($novoStatus, MovimentacaoDaSolicitacao::STATUS)) {
            $this->error('Status inválido. Use um destes: '.implode(', ', array_keys(MovimentacaoDaSolicitacao::STATUS)).'.');

            return 1;
        }

        $protocolo = (string) $this->argument('protocolo');
        $chave = MinhasSolicitacoesService::chaveDoProtocolo($protocolo);
        $solicitacao = $chave === null
            ? null
            : MinhasSolicitacoesService::TIPOS[$chave]['modelo']::where('protocolo', $protocolo)->first();

        if ($solicitacao === null) {
            $this->error('Nenhuma solicitação encontrada com esse protocolo.');

            return 1;
        }

        try {
            $statusDaSolicitacaoService->alterarStatus($solicitacao, $novoStatus, $this->argument('mensagem'), 'comando artisan');
        } catch (TransicaoDeStatusInvalida $erro) {
            $this->error($erro->getMessage());

            return 1;
        }

        $this->info(sprintf('%s agora está "%s".', $protocolo, MovimentacaoDaSolicitacao::rotuloDoStatus($novoStatus)));

        return 0;
    },
)->purpose('Muda o status de uma solicitação e registra a movimentação (somente fora de produção)');
