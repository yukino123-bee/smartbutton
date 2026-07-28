<?php

namespace App\Providers;

use App\Models\Incident;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer(['layouts.clinic', 'layouts.ndrrmo', 'clinic*', 'ndrrmo*'], function ($view) {
            $count = 0;
            if (Schema::hasTable('incidents')) {
                $query = Incident::active();

                if (auth()->user()?->role === 'Clinic') {
                    $query->clinicRelevant();
                }

                $count = $query->count();
            }
            $view->with('activeAlertsCount', $count);
        });
    }
}
