<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManifestacaoOuvidoria extends Model
{
    /**
     * O formulário é um só. Este mapa define o que varia por tipo: os textos e
     * quais campos específicos existem. 'descricao', 'orgao_ou_setor' e os
     * anexos são comuns a todos os tipos e não entram em 'campos'.
     */
    public const TIPOS = [
        'reclamacao' => [
            'rotulo' => 'Reclamação',
            'orientacao' => 'Relate sua insatisfação com um serviço público ou com o atendimento recebido.',
            'rotulo_da_descricao' => 'Descreva a reclamação',
            'campos' => ['data_do_fato', 'local_do_fato'],
        ],
        'denuncia' => [
            'rotulo' => 'Denúncia',
            'orientacao' => 'Comunique irregularidades, abusos ou práticas inadequadas na administração pública.',
            'rotulo_da_descricao' => 'Descreva o que aconteceu',
            'rotulo_do_servidor_ou_setor' => 'Servidor ou setor envolvido',
            'campos' => ['data_do_fato', 'local_do_fato', 'servidor_ou_setor'],
        ],
        'sugestao' => [
            'rotulo' => 'Sugestão',
            'orientacao' => 'Apresente uma ideia para melhorar serviços, procedimentos ou políticas públicas.',
            'rotulo_da_descricao' => 'Descreva a sua sugestão',
            'campos' => [],
        ],
        'elogio' => [
            'rotulo' => 'Elogio',
            'orientacao' => 'Reconheça um bom atendimento ou um serviço bem prestado.',
            'rotulo_da_descricao' => 'Descreva o seu elogio',
            'rotulo_do_servidor_ou_setor' => 'Servidor ou setor elogiado',
            'campos' => ['servidor_ou_setor'],
        ],
    ];

    protected $table = 'manifestacoes_ouvidoria';

    protected $fillable = [
        'user_id',
        'protocolo',
        'tipo',
        'orgao_ou_setor',
        'descricao',
        'data_do_fato',
        'local_do_fato',
        'servidor_ou_setor',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_do_fato' => 'date',
        ];
    }

    public static function tipoTemCampo(string $tipo, string $campo): bool
    {
        return in_array($campo, self::TIPOS[$tipo]['campos'], true);
    }

    public function rotuloDoTipo(): string
    {
        return self::TIPOS[$this->tipo]['rotulo'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(AnexoManifestacaoOuvidoria::class, 'manifestacao_id');
    }
}
