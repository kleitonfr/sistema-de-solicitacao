<?php

namespace App\Services;

use App\Models\SolicitacaoEsic;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EsicService
{
    public function registrarSolicitacao(array $dados, ?UploadedFile $anexo): SolicitacaoEsic
    {
        return DB::transaction(function () use ($dados, $anexo) {
            return SolicitacaoEsic::create([
                'protocolo' => $this->gerarProximoProtocolo(),
                'assunto' => $dados['assunto'],
                'descricao' => $dados['descricao'],
                'anexo_nome_original' => $anexo?->getClientOriginalName(),
                'anexo_caminho' => $anexo?->store('solicitacoes-esic', 'public'),
            ]);
        });
    }

    private function gerarProximoProtocolo(): string
    {
        $ano = now()->year;

        $quantidadeNoAno = SolicitacaoEsic::whereYear('created_at', $ano)
            ->lockForUpdate()
            ->count();

        $sequencial = str_pad((string) ($quantidadeNoAno + 1), 6, '0', STR_PAD_LEFT);

        return "ESIC-{$ano}-{$sequencial}";
    }
}
