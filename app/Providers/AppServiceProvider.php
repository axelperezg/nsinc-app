<?php

namespace App\Providers;

use App\Models\Estrategy;
use App\Models\PlanNacionalDesarrollo;
use App\Observers\EstrategyObserver;
use App\Observers\PlanNacionalDesarrolloObserver;
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
        // Registrar observers
        PlanNacionalDesarrollo::observe(PlanNacionalDesarrolloObserver::class);
        Estrategy::observe(EstrategyObserver::class);
    }
}
