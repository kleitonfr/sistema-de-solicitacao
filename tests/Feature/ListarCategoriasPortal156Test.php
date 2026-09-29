<?php

namespace Tests\Feature;

use App\Models\CategoriaServico;
use App\Models\User;
use Database\Seeders\CategoriaServicoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListarCategoriasPortal156Test extends TestCase
{
    use RefreshDatabase;

    private const ROTA = 'servicos.156.categorias';

    protected function setUp(): void
    {
        parent::setUp();

        // Os testes validam o HTML do servidor; não dependem do build de assets.
        $this->withoutVite();
    }

    private function acessarComoCidadao(array $parametros = [])
    {
        return $this->actingAs(User::factory()->create())
            ->get(route(self::ROTA, $parametros));
    }

    private function criarCategoria(string $nome, bool $emDestaque = false, string $descricao = 'Descrição da categoria.'): void
    {
        CategoriaServico::create([
            'nome' => $nome,
            'descricao' => $descricao,
            'em_destaque' => $emDestaque,
        ]);
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_entrar(): void
    {
        $this->get(route(self::ROTA))->assertRedirect(route('acesso.index'));
    }

    public function test_lista_todas_as_categorias_em_ordem_alfabetica(): void
    {
        $this->criarCategoria('Trânsito', emDestaque: true);
        $this->criarCategoria('IPTU');
        $this->criarCategoria('Árvore');
        $this->criarCategoria('Iluminação Pública');
        $this->criarCategoria('Acessibilidade');

        $this->acessarComoCidadao()
            ->assertOk()
            ->assertSeeInOrder(['Acessibilidade', 'Árvore', 'Iluminação Pública', 'IPTU', 'Trânsito'])
            ->assertSee('5 categorias');
    }

    public function test_filtro_principais_exibe_somente_categorias_em_destaque(): void
    {
        $this->criarCategoria('Iluminação Pública', emDestaque: true);
        $this->criarCategoria('Alvará Comercial');

        $this->acessarComoCidadao(['filtro' => 'principais'])
            ->assertOk()
            ->assertSee('Iluminação Pública')
            ->assertDontSee('Alvará Comercial');
    }

    public function test_busca_encontra_categorias_pelo_nome_ou_pela_descricao(): void
    {
        $this->criarCategoria('Coleta', descricao: 'Coleta de entulhos.');
        $this->criarCategoria('Calçadas', descricao: 'Reconstrução de calçadas e entulhos na via.');
        $this->criarCategoria('IPTU', descricao: 'Informações sobre tributação.');

        $this->acessarComoCidadao(['busca' => 'entulhos'])
            ->assertOk()
            ->assertSee('Coleta')
            ->assertSee('Calçadas')
            ->assertDontSee('Informações sobre tributação.')
            ->assertSee('2 categorias encontradas');
    }

    public function test_busca_trata_caracteres_curinga_como_texto(): void
    {
        $this->criarCategoria('Árvore');
        $this->criarCategoria('Desconto de 50% no IPTU');

        $this->acessarComoCidadao(['busca' => '_'])
            ->assertOk()
            ->assertSee('Não encontramos categorias com esse termo.');

        $this->acessarComoCidadao(['busca' => '%'])
            ->assertOk()
            ->assertSee('Desconto de 50% no IPTU')
            ->assertDontSee('Árvore')
            ->assertSee('1 categoria encontrada');
    }

    public function test_busca_sem_resultado_exibe_estado_vazio_com_opcao_de_limpar(): void
    {
        $this->criarCategoria('Animais');

        $this->acessarComoCidadao(['busca' => 'inexistente'])
            ->assertOk()
            ->assertSee('Não encontramos categorias com esse termo.')
            ->assertSee('Limpar busca');
    }

    public function test_sem_categorias_cadastradas_exibe_estado_vazio(): void
    {
        $this->acessarComoCidadao()
            ->assertOk()
            ->assertSee('Nenhuma categoria disponível no momento.');
    }

    public function test_busca_acima_do_limite_retorna_mensagem_de_erro(): void
    {
        $this->acessarComoCidadao(['busca' => str_repeat('a', 101)])
            ->assertRedirect(route(self::ROTA))
            ->assertSessionHasErrors(['busca' => 'O termo de busca deve ter no máximo 100 caracteres.']);
    }

    public function test_filtro_invalido_retorna_mensagem_de_erro(): void
    {
        $this->acessarComoCidadao(['filtro' => 'qualquer'])
            ->assertRedirect(route(self::ROTA))
            ->assertSessionHasErrors('filtro');
    }

    public function test_seeder_pode_ser_executado_mais_de_uma_vez_sem_duplicar(): void
    {
        $this->seed(CategoriaServicoSeeder::class);
        $this->seed(CategoriaServicoSeeder::class);

        $this->assertSame(14, CategoriaServico::count());
        $this->assertSame(10, CategoriaServico::query()->somenteEmDestaque()->count());
    }
}
