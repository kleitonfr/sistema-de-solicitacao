<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntrarRequest extends FormRequest
{
    protected $redirectRoute = 'acesso.index';

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'senha' => ['required', 'string'],
        ];
    }
}
