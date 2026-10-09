<?php

namespace App\Providers;

use App\Models\ManifestacaoOuvidoria;
use App\Models\Solicitacao156;
use App\Models\SolicitacaoEsic;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // O histórico guarda estes apelidos em vez do nome da classe, para que
        // mover ou renomear um model não invalide os registros existentes.
        Relation::morphMap([
            'solicitacao_156' => Solicitacao156::class,
            'solicitacao_esic' => SolicitacaoEsic::class,
            'manifestacao_ouvidoria' => ManifestacaoOuvidoria::class,
        ]);
    }
}
