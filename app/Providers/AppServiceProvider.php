<?php

namespace App\Providers;
use App\Models\Tarjeta;
use App\Observers\TarjetaObserver;
use Illuminate\Support\Facades\Log;


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
        Tarjeta::observe(TarjetaObserver::class);
    }
}
