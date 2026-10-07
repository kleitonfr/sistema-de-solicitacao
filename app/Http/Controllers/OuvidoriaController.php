<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarManifestacaoRequest;
use App\Models\ManifestacaoOuvidoria;
use App\Services\OuvidoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class OuvidoriaController extends Controller
{
    public function __construct(private readonly OuvidoriaService $ouvidoriaService)
    {
    }

    public function index(): View
    {
        return view('servicos.ouvidoria.ouvidoria-pagina-inicial');
    }

    public function criarManifestacao(string $tipo): View
    {
        return view('servicos.ouvidoria.manifestacao.criar', [
            'tipo' => $tipo,
            'configuracaoDoTipo' => ManifestacaoOuvidoria::TIPOS[$tipo],
        ]);
    }

    public function registrarManifestacao(RegistrarManifestacaoRequest $request, string $tipo): RedirectResponse
    {
        $manifestacao = $this->ouvidoriaService->registrarManifestacao(
            $request->user(),
            $tipo,
            $request->validated(),
            Arr::wrap($request->file('anexos')),
        );

        return redirect()->route('ouvidoria.manifestacao.confirmacao', $manifestacao->protocolo);
    }

    public function confirmacaoManifestacao(Request $request, string $protocolo): View
    {
        return view('servicos.ouvidoria.manifestacao.confirmacao', [
            'manifestacao' => $this->ouvidoriaService->buscarManifestacaoDoUsuario($request->user(), $protocolo),
        ]);
    }
}
