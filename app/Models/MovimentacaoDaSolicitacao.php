<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Registro do histórico de uma solicitação (Portal 156, e-SIC ou Ouvidoria).
 * Só recebe inclusões: um registro nunca é alterado, por isso não existe
 * updated_at. Também guarda o vocabulário e as regras de status comuns aos
 * três serviços.
 */
class MovimentacaoDaSolicitacao extends Model
{
    public const UPDATED_AT = null;

    public const STATUS_INICIAL = 'recebida';

    public const STATUS = [
        'recebida' => 'Recebida',
        'em_analise' => 'Em análise',
        'encaminhada' => 'Encaminhada',
        'em_atendimento' => 'Em atendimento',
        'concluida' => 'Concluída',
    ];

    /**
     * Não há retrocesso, e só "em análise" é obrigatória depois de
     * "recebida". Concluída é final.
     */
    private const TRANSICOES_PERMITIDAS = [
        'recebida' => ['em_analise'],
        'em_analise' => ['encaminhada', 'em_atendimento', 'concluida'],
        'encaminhada' => ['em_atendimento', 'concluida'],
        'em_atendimento' => ['concluida'],
        'concluida' => [],
    ];

    protected $table = 'movimentacoes_solicitacao';

    protected $fillable = [
        'status_anterior',
        'status_novo',
        'mensagem',
        'registrado_por',
    ];

    public static function rotuloDoStatus(string $status): string
    {
        return self::STATUS[$status] ?? $status;
    }

    public static function transicaoEhPermitida(string $statusAtual, string $novoStatus): bool
    {
        return in_array($novoStatus, self::TRANSICOES_PERMITIDAS[$statusAtual] ?? [], true);
    }

    public function solicitacao(): MorphTo
    {
        return $this->morphTo('solicitacao');
    }
}
