<?php

namespace App\Services;

use App\Exceptions\SolicitacaoNaoPodeSerRegistrada;
use App\Logging\ContextoDaFalha;
use App\Logging\RegistroDeSeguranca;
use App\Models\CategoriaServico;
use App\Models\Servico156;
use App\Models\Solicitacao156;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class Servico156Service
{
    private const PREFIXO_DO_PROTOCOLO = '156';

    public function __construct(
        private readonly GeradorDeProtocolo $geradorDeProtocolo,
        private readonly ArmazenadorDeAnexos $armazenadorDeAnexos,
        private readonly RegistroDeSeguranca $registroDeSeguranca,
        private readonly StatusDaSolicitacaoService $statusDaSolicitacaoService,
    ) {
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
     *
     * @throws SolicitacaoNaoPodeSerRegistrada
     */
    public function registrarSolicitacao(User $usuario, Servico156 $servico, array $dados, array $anexos): Solicitacao156
    {
        $caminhosGravados = [];

        DB::beginTransaction();

        try {
            $solicitacao = Solicitacao156::create([
                ...Arr::only($dados, ['descricao', 'telefone_contato', 'cep', 'logradouro', 'numero', 'bairro', 'ponto_referencia']),
                'user_id' => $usuario->id,
                'servico156_id' => $servico->id,
                'categoria_servico_id' => $servico->categoria_servico_id,
                'protocolo' => $this->geradorDeProtocolo->gerar(self::PREFIXO_DO_PROTOCOLO, Solicitacao156::class),
            ]);

            $this->statusDaSolicitacaoService->registrarRecebimento($solicitacao);

            foreach ($anexos as $anexo) {
                $caminho = $this->armazenadorDeAnexos->armazenar($anexo, "solicitacoes-156/{$solicitacao->protocolo}");
                $caminhosGravados[] = $caminho;

                $solicitacao->anexos()->create([
                    'nome_original' => $anexo->getClientOriginalName(),
                    'caminho' => $caminho,
                ]);
            }

            DB::commit();
        } catch (Throwable $erro) {
            DB::rollBack();
            $this->armazenadorDeAnexos->apagar($caminhosGravados);

            Log::error('solicitacao_156_nao_registrada', [
                ...ContextoDaFalha::deExcecao($erro),
                'usuario_id' => $usuario->id,
                'servico_id' => $servico->id,
                'quantidade_de_anexos' => count($anexos),
            ]);

            throw SolicitacaoNaoPodeSerRegistrada::por($erro);
        }

        Log::info('solicitacao_156_registrada', [
            'usuario_id' => $usuario->id,
            'protocolo' => $solicitacao->protocolo,
            'servico_id' => $servico->id,
            'quantidade_de_anexos' => count($anexos),
        ]);

        return $solicitacao;
    }

    public function buscarSolicitacaoDoUsuario(User $usuario, string $protocolo): Solicitacao156
    {
        try {
            return $usuario->solicitacoes156()
                ->with(['servico', 'categoria', 'anexos'])
                ->where('protocolo', $protocolo)
                ->firstOrFail();
        } catch (ModelNotFoundException $erro) {
            $this->registroDeSeguranca->aviso('solicitacao_156_nao_encontrada_para_o_usuario', [
                'usuario_id' => $usuario->id,
                'protocolo' => $protocolo,
            ]);

            throw $erro;
        }
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
