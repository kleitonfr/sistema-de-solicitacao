<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidaCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', (string) $value);

        if (! $this->possuiFormatoValido($cpf) || ! $this->possuiDigitosVerificadoresValidos($cpf)) {
            $fail('O :attribute informado não é válido.');
        }
    }

    private function possuiFormatoValido(string $cpf): bool
    {
        return strlen($cpf) === 11 && ! preg_match('/^(\d)\1{10}$/', $cpf);
    }

    private function possuiDigitosVerificadoresValidos(string $cpf): bool
    {
        for ($posicaoDoDigito = 9; $posicaoDoDigito <= 10; $posicaoDoDigito++) {
            $soma = 0;

            for ($indice = 0; $indice < $posicaoDoDigito; $indice++) {
                $soma += (int) $cpf[$indice] * (($posicaoDoDigito + 1) - $indice);
            }

            $resto = $soma % 11;
            $digitoEsperado = $resto < 2 ? 0 : 11 - $resto;

            if ((int) $cpf[$posicaoDoDigito] !== $digitoEsperado) {
                return false;
            }
        }

        return true;
    }
}
