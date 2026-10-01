<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitacaoEsic extends Model
{
    protected $table = 'solicitacoes_esic';

    protected $fillable = [
        'protocolo',
        'assunto',
        'descricao',
        'anexo_nome_original',
        'anexo_caminho',
    ];

    public const ASSUNTOS = [
        'outro_tema' => 'Outro tema',
        'contrato' => 'Contrato',
        'despesas' => 'Despesas',
        'funcionarios' => 'Funcionários',
        'licitacoes' => 'Licitações',
        'patrimonio' => 'Patrimônio',
        'processo_administrativo' => 'Processo Administrativo',
    ];

    public function rotuloDoAssunto(): string
    {
        return self::ASSUNTOS[$this->assunto] ?? $this->assunto;
    }

    /**
     * Gera um protocolo no formato ESIC-AAAA-NNNNNN. O número sequencial é
     * uma string de 6 dígitos preenchida com zeros à esquerda, derivada da
     * contagem de solicitações já existentes no ano corrente — suficiente
     * para simular um protocolo real neste estágio (sem integração com a
     * API), mas não é uma garantia de unicidade sob concorrência real.
     */
    public static function gerarProtocolo(): string
    {
        $ano = now()->year;

        $quantidadeNoAno = self::whereYear('created_at', $ano)->count();

        $sequencial = str_pad((string) ($quantidadeNoAno + 1), 6, '0', STR_PAD_LEFT);

        return "ESIC-{$ano}-{$sequencial}";
    }
}
