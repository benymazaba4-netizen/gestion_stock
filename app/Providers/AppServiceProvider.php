<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
// Importation nécessaire pour les schémas de base de données (utile pour Aiven)
use Illuminate\Support\Facades\Schema; 

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
        // 1. Force le HTTPS en production (Render)
        // Cela règle le problème du bouton qui ne réagit pas
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // 2. Correction pour MySQL/Aiven
        // Empêche les erreurs de longueur de clé d'index lors des migrations
        Schema::defaultStringLength(191);
    }
}
