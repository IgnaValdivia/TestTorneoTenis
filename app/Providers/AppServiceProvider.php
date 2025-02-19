<?php

namespace App\Providers;

use App\Interfaces\IJugadorFemeninoService;
use App\Interfaces\IJugadorMasculinoService;
use App\Interfaces\IJugadorService;
use App\Interfaces\ITorneoService;
use App\Interfaces\Repositories\IJugadorFemeninoRepository;
use App\Interfaces\Repositories\IJugadorMasculinoRepository;
use App\Interfaces\Repositories\IJugadorRepository;
use App\Interfaces\Repositories\IPartidaRepository;
use App\Interfaces\Repositories\ITorneoRepository;
use App\Repositories\JugadorFemeninoRepository;
use App\Repositories\JugadorMasculinoRepository;
use App\Repositories\JugadorRepository;
use App\Repositories\PartidaRepository;
use App\Repositories\TorneoRepository;
use App\Services\JugadorFemeninoService;
use App\Services\JugadorMasculinoService;
use App\Services\JugadorService;
use App\Services\TorneoService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ITorneoRepository::class, TorneoRepository::class);
        $this->app->bind(IPartidaRepository::class, PartidaRepository::class);
        $this->app->bind(IJugadorMasculinoRepository::class, JugadorMasculinoRepository::class);
        $this->app->bind(IJugadorFemeninoRepository::class, JugadorFemeninoRepository::class);
        $this->app->bind(IJugadorRepository::class, JugadorRepository::class);

        $this->app->bind(ITorneoService::class, TorneoService::class);
        $this->app->bind(IJugadorService::class, JugadorService::class);
        $this->app->bind(IJugadorMasculinoService::class, JugadorMasculinoService::class);
        $this->app->bind(IJugadorFemeninoService::class, JugadorFemeninoService::class);
        $this->app->bind(TorneoService::class, function ($app) {
            return new TorneoService(
                $app->make(ITorneoRepository::class),
                $app->make(IPartidaRepository::class),
                $app->make(IJugadorService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
