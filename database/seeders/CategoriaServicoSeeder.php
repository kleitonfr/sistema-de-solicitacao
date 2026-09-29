<?php

namespace Database\Seeders;

use App\Models\CategoriaServico;
use Illuminate\Database\Seeder;

/**
 * Dados PROVISÓRIOS: categorias copiadas da Central 156 de Curitiba
 * (156.curitiba.pr.gov.br), usadas como referência até a definição das
 * categorias oficiais de Caraguatatuba.
 *
 * `updateOrCreate` pelo nome permite rodar o seeder mais de uma vez sem
 * duplicar registros.
 */
class CategoriaServicoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->categorias() as $categoria) {
            CategoriaServico::updateOrCreate(
                ['nome' => $categoria['nome']],
                $categoria,
            );
        }
    }

    /**
     * @return list<array{nome: string, descricao: string, em_destaque: bool}>
     */
    private function categorias(): array
    {
        return [
            [
                'nome' => 'Abordagem Social - Situação de Rua',
                'descricao' => 'Serviço referente aos pedidos de abordagem social a pessoas adultas, idosas, crianças e adolescentes em situação de rua.',
                'em_destaque' => false,
            ],
            [
                'nome' => 'Acessibilidade',
                'descricao' => 'Serviço referente aos pedidos de elaboração de projetos ou estudos de implantação de elevadores em terminais e estações-tubo do transporte coletivo e rampas de acesso em vias públicas.',
                'em_destaque' => false,
            ],
            [
                'nome' => 'Alvará Comercial',
                'descricao' => 'Para manifestações sobre Alvará Comercial.',
                'em_destaque' => false,
            ],
            [
                'nome' => 'Animais',
                'descricao' => 'Serviço referente aos pedidos de fiscalização quanto a cães bravos, avançando nas pessoas em via pública, e denúncias de maus tratos a animais domésticos.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Armazém da Família',
                'descricao' => 'Serviço referente aos pedidos relacionados aos Armazéns da Família, abrangendo situações relacionadas a segurança, estrutura, manutenção, preços, variedade de produtos e atendimento ao público.',
                'em_destaque' => false,
            ],
            [
                'nome' => 'Árvore',
                'descricao' => 'O serviço abrange solicitações relacionadas a árvores ou galhos caídos em vias públicas (com ou sem bloqueio) e avaliação para corte em vias públicas.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Calçadas',
                'descricao' => 'Serviço referente aos pedidos de fiscalização em terrenos baldios e edificados para a construção ou reconstrução de calçadas.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Coleta',
                'descricao' => 'Serviço referente aos pedidos de coleta de caliças (restos de construção) e entulhos diversos, limitados a 5 carrinhos de mão.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Disque Solidariedade',
                'descricao' => 'Serviço referente aos pedidos de agendamento para o recolhimento de doações de equipamentos eletrônicos, eletrodomésticos e móveis em condições de uso.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Fiscalização do comércio estabelecido',
                'descricao' => 'Serviço referente aos pedidos de fiscalização em estabelecimentos comerciais e templos religiosos, quanto à regularidade e ao cumprimento das determinações.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Iluminação Pública',
                'descricao' => 'Serviço referente aos pedidos de manutenção dos pontos de iluminação da rede pública, como a troca de lâmpadas e inspeção dos componentes das luminárias.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'IPTU',
                'descricao' => 'Serviço referente aos pedidos de informações, reclamações e sugestões sobre IPTU e Taxa de coleta de lixo.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Pavimentação',
                'descricao' => 'Serviço referente aos pedidos de análise técnica e orçamentária para implantação de pavimentação asfáltica.',
                'em_destaque' => true,
            ],
            [
                'nome' => 'Trânsito',
                'descricao' => 'Serviço referente aos pedidos de fiscalização de trânsito quanto a estacionamento irregular, bloqueios de pista e veículos abandonados na via pública.',
                'em_destaque' => true,
            ],
        ];
    }
}
