<?php

namespace App\Services;

use App\Models\CategoriaServico;
use App\Models\Servico156;
use App\Models\Solicitacao156;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Servico156Service
{
    private const DISCO_DOS_ANEXOS = 'local';

    private const PREFIXO_DO_PROTOCOLO = '156';

    public function __construct(private readonly GeradorDeProtocolo $geradorDeProtocolo)
    {
    }

    /**
     * @return Collection<int, CategoriaServico>
     */
    public function listarCategorias(?string $termoDeBusca, bool $somenteEmDestaque): Collection
    {
        return CategoriaServico::query()
            ->when($somenteEmDestaque, fn ($consulta) => $consulta->somenteEmDestaque())
            ->get()
            ->when(
                $termoDeBusca !== null,
                fn (Collection $categorias) => $categorias->filter(
                    fn (CategoriaServico $categoria) => $this->contemTermo("{$categoria->nome} {$categoria->descricao}", $termoDeBusca),
                ),
            )
            ->sortBy(fn (CategoriaServico $categoria) => $this->normalizar($categoria->nome))
            ->values();
    }

    public function buscarCategoriaPorSlug(string $slug): CategoriaServico
    {
        return CategoriaServico::where('slug', $slug)->firstOrFail();
    }

    /**
     * @return Collection<int, Servico156>
     */
    public function listarServicosDaCategoria(CategoriaServico $categoria): Collection
    {
        return $categoria->servicos
            ->sortBy(fn (Servico156 $servico) => $this->normalizar($servico->nome))
            ->values();
    }

    public function buscarServicoPorSlug(string $slug): Servico156
    {
        return Servico156::with('categoria')->where('slug', $slug)->firstOrFail();
    }

    /**
     * @param  list<UploadedFile>  $anexos
     */
    public function registrarSolicitacao(User $usuario, Servico156 $servico, array $dados, array $anexos): Solicitacao156
    {
        return DB::transaction(function () use ($usuario, $servico, $dados, $anexos) {
            $solicitacao = Solicitacao156::create([
                ...Arr::only($dados, ['descricao', 'telefone_contato', 'cep', 'logradouro', 'numero', 'bairro', 'ponto_referencia']),
                'user_id' => $usuario->id,
                'servico156_id' => $servico->id,
                'categoria_servico_id' => $servico->categoria_servico_id,
                'protocolo' => $this->geradorDeProtocolo->gerar(self::PREFIXO_DO_PROTOCOLO, Solicitacao156::class),
            ]);

            foreach ($anexos as $anexo) {
                $solicitacao->anexos()->create([
                    'nome_original' => $anexo->getClientOriginalName(),
                    'caminho' => $anexo->store("solicitacoes-156/{$solicitacao->protocolo}", self::DISCO_DOS_ANEXOS),
                ]);
            }

            return $solicitacao;
        });
    }

    public function buscarSolicitacaoDoUsuario(User $usuario, string $protocolo): Solicitacao156
    {
        return $usuario->solicitacoes156()
            ->with(['servico', 'categoria', 'anexos'])
            ->where('protocolo', $protocolo)
            ->firstOrFail();
    }

    private function contemTermo(string $texto, string $termo): bool
    {
        return str_contains($this->normalizar($texto), $this->normalizar($termo));
    }

    /**
     * Busca e ordenação ignoram acento e caixa no PHP porque o SQLite compara
     * bytes e o MySQL aplica a collation: no banco, o resultado mudaria
     * conforme o ambiente.
     */
    private function normalizar(string $texto): string
    {
        return mb_strtolower(Str::ascii($texto));
    }
}
