<?php

namespace App\Services;

use App\Models\SolicitacaoEsic;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class EsicService
{
    private const DISCO_DOS_ANEXOS = 'local';

    private const PREFIXO_DO_PROTOCOLO = 'ESIC';

    public function __construct(private readonly GeradorDeProtocolo $geradorDeProtocolo)
    {
    }

    public function registrarSolicitacao(User $usuario, array $dados, ?UploadedFile $anexo): SolicitacaoEsic
    {
        return DB::transaction(function () use ($usuario, $dados, $anexo) {
            return SolicitacaoEsic::create([
                'user_id' => $usuario->id,
                'protocolo' => $this->geradorDeProtocolo->gerar(self::PREFIXO_DO_PROTOCOLO, SolicitacaoEsic::class),
                'assunto' => $dados['assunto'],
                'descricao' => $dados['descricao'],
                'anexo_nome_original' => $anexo?->getClientOriginalName(),
                'anexo_caminho' => $anexo?->store('solicitacoes-esic', self::DISCO_DOS_ANEXOS),
            ]);
        });
    }

    public function buscarSolicitacaoDoUsuario(User $usuario, string $protocolo): SolicitacaoEsic
    {
        return $usuario->solicitacoesEsic()->where('protocolo', $protocolo)->firstOrFail();
    }
}
