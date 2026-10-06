<?php

namespace Database\Seeders;

use App\Models\CategoriaServico;
use App\Models\Servico156;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Dados PROVISÓRIOS, derivados das descrições das categorias provisórias
 * (ver CategoriaServicoSeeder) até a definição do catálogo oficial.
 *
 * IPTU e Alvará Comercial ficam sem serviços de propósito: o formulário
 * único pede localização, o que não faz sentido para serviços de informação.
 */
class Servico156Seeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->servicosPorCategoria() as $nomeDaCategoria => $servicos) {
            $categoria = CategoriaServico::where('nome', $nomeDaCategoria)->firstOrFail();

            foreach ($servicos as $servico) {
                Servico156::updateOrCreate(
                    ['categoria_servico_id' => $categoria->id, 'nome' => $servico['nome']],
                    [...$servico, 'slug' => Str::slug($servico['nome'])],
                );
            }
        }
    }

    /**
     * @return array<string, list<array{nome: string, descricao: string}>>
     */
    private function servicosPorCategoria(): array
    {
        return [
            'Abordagem Social - Situação de Rua' => [
                [
                    'nome' => 'Abordagem a pessoa adulta ou idosa em situação de rua',
                    'descricao' => 'Solicite a abordagem social de pessoa adulta ou idosa que esteja em situação de rua.',
                ],
                [
                    'nome' => 'Abordagem a criança ou adolescente em situação de rua',
                    'descricao' => 'Solicite a abordagem social de criança ou adolescente que esteja em situação de rua.',
                ],
            ],
            'Acessibilidade' => [
                [
                    'nome' => 'Rampa de acesso em via pública',
                    'descricao' => 'Solicite estudo ou projeto para implantação de rampa de acesso em via pública.',
                ],
                [
                    'nome' => 'Elevador em terminal ou estação de transporte coletivo',
                    'descricao' => 'Solicite estudo ou projeto para implantação de elevador em terminal ou estação-tubo.',
                ],
            ],
            'Animais' => [
                [
                    'nome' => 'Cão bravo em via pública',
                    'descricao' => 'Solicite fiscalização de cão que esteja avançando em pessoas em via pública.',
                ],
                [
                    'nome' => 'Maus-tratos a animal doméstico',
                    'descricao' => 'Denuncie maus-tratos a animal doméstico.',
                ],
            ],
            'Armazém da Família' => [
                [
                    'nome' => 'Segurança, estrutura e manutenção do armazém',
                    'descricao' => 'Informe problemas de segurança, estrutura ou manutenção em um Armazém da Família.',
                ],
                [
                    'nome' => 'Preços, variedade de produtos e atendimento',
                    'descricao' => 'Registre questões sobre preços, variedade de produtos ou atendimento ao público.',
                ],
            ],
            'Árvore' => [
                [
                    'nome' => 'Árvore ou galho caído com bloqueio da via',
                    'descricao' => 'Informe árvore ou galho caído que esteja bloqueando a via pública.',
                ],
                [
                    'nome' => 'Árvore ou galho caído sem bloqueio da via',
                    'descricao' => 'Informe árvore ou galho caído em via pública que não bloqueie a passagem.',
                ],
                [
                    'nome' => 'Avaliação para corte de árvore',
                    'descricao' => 'Solicite a avaliação técnica para o corte de árvore em via pública.',
                ],
            ],
            'Calçadas' => [
                [
                    'nome' => 'Fiscalização de calçada em terreno baldio',
                    'descricao' => 'Solicite fiscalização para construção ou reconstrução de calçada em terreno baldio.',
                ],
                [
                    'nome' => 'Fiscalização de calçada em terreno edificado',
                    'descricao' => 'Solicite fiscalização para construção ou reconstrução de calçada em terreno edificado.',
                ],
            ],
            'Coleta' => [
                [
                    'nome' => 'Coleta de caliça',
                    'descricao' => 'Solicite a coleta de caliça (restos de construção), limitada a 5 carrinhos de mão.',
                ],
                [
                    'nome' => 'Coleta de entulhos diversos',
                    'descricao' => 'Solicite a coleta de entulhos diversos, limitada a 5 carrinhos de mão.',
                ],
            ],
            'Disque Solidariedade' => [
                [
                    'nome' => 'Doação de equipamentos eletrônicos e eletrodomésticos',
                    'descricao' => 'Agende o recolhimento de doação de equipamentos eletrônicos e eletrodomésticos em condições de uso.',
                ],
                [
                    'nome' => 'Doação de móveis',
                    'descricao' => 'Agende o recolhimento de doação de móveis em condições de uso.',
                ],
            ],
            'Fiscalização do comércio estabelecido' => [
                [
                    'nome' => 'Fiscalização de estabelecimento comercial',
                    'descricao' => 'Solicite fiscalização quanto à regularidade e ao cumprimento das determinações em estabelecimento comercial.',
                ],
                [
                    'nome' => 'Fiscalização de templo religioso',
                    'descricao' => 'Solicite fiscalização quanto à regularidade e ao cumprimento das determinações em templo religioso.',
                ],
            ],
            'Iluminação Pública' => [
                [
                    'nome' => 'Troca de lâmpada',
                    'descricao' => 'Solicite a troca de lâmpada de ponto de iluminação da rede pública.',
                ],
                [
                    'nome' => 'Inspeção de luminária',
                    'descricao' => 'Solicite a inspeção dos componentes de luminária da rede pública.',
                ],
            ],
            'Pavimentação' => [
                [
                    'nome' => 'Análise técnica para pavimentação asfáltica',
                    'descricao' => 'Solicite a análise técnica para implantação de pavimentação asfáltica.',
                ],
                [
                    'nome' => 'Análise orçamentária para pavimentação asfáltica',
                    'descricao' => 'Solicite a análise orçamentária para implantação de pavimentação asfáltica.',
                ],
            ],
            'Trânsito' => [
                [
                    'nome' => 'Estacionamento irregular',
                    'descricao' => 'Solicite fiscalização de veículo estacionado de forma irregular.',
                ],
                [
                    'nome' => 'Bloqueio de pista',
                    'descricao' => 'Informe bloqueio de pista que prejudique a circulação.',
                ],
                [
                    'nome' => 'Veículo abandonado na via pública',
                    'descricao' => 'Informe veículo abandonado na via pública.',
                ],
            ],
        ];
    }
}
