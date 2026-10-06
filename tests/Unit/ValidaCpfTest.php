<?php

namespace Tests\Unit;

use App\Rules\ValidaCpf;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ValidaCpfTest extends TestCase
{
    #[DataProvider('cpfsValidos')]
    public function test_aceita_cpf_valido(string $cpf): void
    {
        $this->assertTrue($this->cpfPassaNaValidacao($cpf));
    }

    #[DataProvider('cpfsInvalidos')]
    public function test_rejeita_cpf_invalido(string $cpf): void
    {
        $this->assertFalse($this->cpfPassaNaValidacao($cpf));
    }

    public static function cpfsValidos(): array
    {
        return [
            'com máscara' => ['529.982.247-25'],
            'sem máscara' => ['52998224725'],
        ];
    }

    public static function cpfsInvalidos(): array
    {
        return [
            'primeiro dígito verificador errado' => ['529.982.247-35'],
            'segundo dígito verificador errado' => ['529.982.247-26'],
            'sequência repetida' => ['111.111.111-11'],
            'menos de 11 dígitos' => ['529.982.247'],
            'mais de 11 dígitos' => ['529.982.247-250'],
            'sem dígitos' => ['abc.def.ghi-jk'],
        ];
    }

    private function cpfPassaNaValidacao(string $cpf): bool
    {
        return Validator::make(['cpf' => $cpf], ['cpf' => [new ValidaCpf()]])->passes();
    }
}
