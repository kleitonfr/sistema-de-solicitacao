<?php

namespace App\Services;

use App\Exceptions\AnexoIndisponivel;
use App\Logging\RegistroDeSeguranca;
use App\Models\ManifestacaoOuvidoria;
use App\Models\Solicitacao156;
use App\Models\SolicitacaoEsic;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MinhasSolicitacoesService
{
    public const PORTAL_156 = 'portal156';

    public const ESIC = 'esic';

    public const OUVIDORIA = 'ouvidoria';

    /**
     * Os três serviços em que o cidadão registra solicitações. É a única lista
     * que liga protocolo, serviço e model.
     */
    public const TIPOS = [
        self::PORTAL_156 => [
            'rotulo' => 'Portal 156',
            'prefixo_do_protocolo' => '156',
            'modelo' => Solicitacao156::class,
        ],
        self::ESIC => [
            'rotulo' => 'e-SIC',
            'prefixo_do_protocolo' => 'ESIC',
            'modelo' => SolicitacaoEsic::class,
        ],
        self::OUVIDORIA => [
            'rotulo' => 'Ouvidoria',
            'prefixo_do_protocolo' => 'OUV',
            'modelo' => ManifestacaoOuvidoria::class,
        ],
    ];

    private const ITENS_POR_PAGINA = 10;

    public function __construct(
        private readonly ArmazenadorDeAnexos $armazenadorDeAnexos,
        private readonly RegistroDeSeguranca $registroDeSeguranca,
    ) {
    }

    /**
     * As três tabelas são consultadas separadamente e unidas aqui: o volume
     * por cidadão é pequeno. Se crescer, vale trocar por uma consulta única.
     *
     * Cada item traz: chaveDoTipo, rotuloDoTipo, titulo, protocolo, status e registradaEm.
     */
    public function listar(User $usuario, ?string $chaveDoTipo, ?string $busca): LengthAwarePaginator
    {
        $resumos = $this->chavesConsultadas($chaveDoTipo)
            ->flatMap(fn (string $chave) => $this->resumirSolicitacoesDoTipo($usuario, $chave, $busca))
            ->sortByDesc(fn (object $resumo) => $resumo->registradaEm->getTimestamp())
            ->values();

        return $this->paginar($resumos);
    }

    public static function chaveDoProtocolo(string $protocolo): ?string
    {
        foreach (self::TIPOS as $chave => $tipo) {
            if (str_starts_with($protocolo, $tipo['prefixo_do_protocolo'].'-')) {
                return $chave;
            }
        }

        return null;
    }

    /**
     * Traz: resumo, campos (rótulo => valor), anexos (posicao e nome) e movimentacoes.
     */
    public function buscarDetalheDoUsuario(User $usuario, string $protocolo): object
    {
        $chave = $this->chaveDoProtocoloOuFalhar($protocolo);
        $solicitacao = $this->buscarSolicitacaoDoUsuario($usuario, $chave, $protocolo);

        $anexos = [];
        foreach ($this->anexosDa($chave, $solicitacao) as $indice => $anexo) {
            $anexos[] = ['posicao' => $indice + 1, 'nome' => $anexo['nome']];
        }

        return (object) [
            'resumo' => $this->resumir($chave, $solicitacao),
            'campos' => $this->camposDa($chave, $solicitacao),
            'anexos' => $anexos,
            'movimentacoes' => $solicitacao->movimentacoes,
        ];
    }

    /**
     * @throws AnexoIndisponivel
     */
    public function baixarAnexoDoUsuario(User $usuario, string $protocolo, int $posicao): StreamedResponse
    {
        $chave = $this->chaveDoProtocoloOuFalhar($protocolo);
        $solicitacao = $this->buscarSolicitacaoDoUsuario($usuario, $chave, $protocolo);

        $anexo = $this->anexosDa($chave, $solicitacao)[$posicao - 1] ?? throw new ModelNotFoundException();

        $contexto = ['usuario_id' => $usuario->id, 'protocolo' => $protocolo, 'posicao_do_anexo' => $posicao];

        try {
            $resposta = $this->armazenadorDeAnexos->baixar($anexo['caminho'], $anexo['nome']);
        } catch (AnexoIndisponivel $erro) {
            Log::error('anexo_ausente_no_disco', $contexto);

            throw $erro;
        }

        Log::info('anexo_baixado', $contexto);

        return $resposta;
    }

    /**
     * @return Collection<int, string>
     */
    private function chavesConsultadas(?string $chaveDoTipo): Collection
    {
        return $chaveDoTipo === null
            ? collect(array_keys(self::TIPOS))
            : collect([$chaveDoTipo]);
    }

    /**
     * @return Collection<int, object>
     */
    private function resumirSolicitacoesDoTipo(User $usuario, string $chave, ?string $busca): Collection
    {
        $modelo = self::TIPOS[$chave]['modelo'];

        return $modelo::query()
            ->where('user_id', $usuario->id)
            ->when($busca !== null, fn ($consulta) => $consulta->where('protocolo', 'like', "%{$busca}%"))
            ->when($chave === self::PORTAL_156, fn ($consulta) => $consulta->with('servico'))
            ->get()
            ->map(fn (Model $solicitacao) => $this->resumir($chave, $solicitacao));
    }

    private function resumir(string $chave, Model $solicitacao): object
    {
        return (object) [
            'chaveDoTipo' => $chave,
            'rotuloDoTipo' => self::TIPOS[$chave]['rotulo'],
            'titulo' => $this->tituloDa($chave, $solicitacao),
            'protocolo' => $solicitacao->protocolo,
            'status' => $solicitacao->status,
            'registradaEm' => $solicitacao->created_at,
        ];
    }

    private function tituloDa(string $chave, Model $solicitacao): string
    {
        return match ($chave) {
            self::PORTAL_156 => $solicitacao->servico->nome,
            self::ESIC => 'Pedido de informação — '.$solicitacao->rotuloDoAssunto(),
            self::OUVIDORIA => $solicitacao->rotuloDoTipo(),
        };
    }

    /**
     * @param  Collection<int, object>  $resumos
     * @return LengthAwarePaginator<int, object>
     */
    private function paginar(Collection $resumos): LengthAwarePaginator
    {
        $paginaAtual = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $resumos->forPage($paginaAtual, self::ITENS_POR_PAGINA)->values(),
            $resumos->count(),
            self::ITENS_POR_PAGINA,
            $paginaAtual,
            ['path' => LengthAwarePaginator::resolveCurrentPath()],
        );
    }

    private function chaveDoProtocoloOuFalhar(string $protocolo): string
    {
        return self::chaveDoProtocolo($protocolo) ?? throw new ModelNotFoundException();
    }

    private function buscarSolicitacaoDoUsuario(User $usuario, string $chave, string $protocolo): Model
    {
        $modelo = self::TIPOS[$chave]['modelo'];

        try {
            return $modelo::query()
                ->where('user_id', $usuario->id)
                ->where('protocolo', $protocolo)
                ->with($this->relacoesDoDetalhe($chave))
                ->firstOrFail();
        } catch (ModelNotFoundException $erro) {
            $this->registroDeSeguranca->aviso('solicitacao_nao_encontrada_para_o_usuario', [
                'usuario_id' => $usuario->id,
                'tipo' => $chave,
                'protocolo' => $protocolo,
            ]);

            throw $erro;
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    private function relacoesDoDetalhe(string $chave): array
    {
        $anexosEmOrdemDeEnvio = fn ($consulta) => $consulta->orderBy('id');

        return match ($chave) {
            self::PORTAL_156 => ['servico', 'categoria', 'anexos' => $anexosEmOrdemDeEnvio, 'movimentacoes'],
            self::ESIC => ['movimentacoes'],
            self::OUVIDORIA => ['anexos' => $anexosEmOrdemDeEnvio, 'movimentacoes'],
        };
    }

    /**
     * Só os campos preenchidos entram, para a tela não exibir linhas vazias.
     *
     * @return array<string, string>
     */
    private function camposDa(string $chave, Model $solicitacao): array
    {
        $campos = match ($chave) {
            self::PORTAL_156 => [
                'Serviço' => $solicitacao->servico->nome,
                'Categoria' => $solicitacao->categoria->nome,
                'Descrição' => $solicitacao->descricao,
                'Telefone de contato' => $solicitacao->telefone_contato,
                'Endereço' => $solicitacao->enderecoCompleto(),
                'Ponto de referência' => $solicitacao->ponto_referencia,
            ],
            self::ESIC => [
                'Assunto' => $solicitacao->rotuloDoAssunto(),
                'Descrição' => $solicitacao->descricao,
            ],
            self::OUVIDORIA => [
                'Tipo' => $solicitacao->rotuloDoTipo(),
                'Órgão ou setor' => $solicitacao->orgao_ou_setor,
                'Data do fato' => $solicitacao->data_do_fato?->format('d/m/Y'),
                'Local do fato' => $solicitacao->local_do_fato,
                $solicitacao::TIPOS[$solicitacao->tipo]['rotulo_do_servidor_ou_setor'] ?? 'Servidor ou setor' => $solicitacao->servidor_ou_setor,
                'Descrição' => $solicitacao->descricao,
            ],
        };

        return array_filter($campos, fn (?string $valor) => $valor !== null && $valor !== '');
    }

    /**
     * O e-SIC guarda um único anexo na própria linha; Portal 156 e Ouvidoria
     * guardam em tabela própria. Aqui os dois formatos viram uma lista só,
     * na ordem de envio.
     *
     * @return list<array{nome: string, caminho: string}>
     */
    private function anexosDa(string $chave, Model $solicitacao): array
    {
        if ($chave === self::ESIC) {
            return $solicitacao->anexo_caminho === null
                ? []
                : [['nome' => $solicitacao->anexo_nome_original, 'caminho' => $solicitacao->anexo_caminho]];
        }

        return $solicitacao->anexos
            ->map(fn (Model $anexo) => ['nome' => $anexo->nome_original, 'caminho' => $anexo->caminho])
            ->values()
            ->all();
    }
}
