<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Incident;

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
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer(['layouts.clinic', 'layouts.ndrrmo', 'clinic*', 'ndrrmo*'], function ($view) {
            $count = 0;
            if (Schema::hasTable('incidents')) {
                $count = Incident::where('emergency_type', 'Critical Emergency')
                    ->whereIn('status', ['pending', 'acknowledged'])
                    ->count();
            }
            $view->with('activeAlertsCount', $count);
        });
    }
}
