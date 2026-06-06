<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Viajes siguen en SQLite/MySQL. Firestore es espejo en tiempo real (FirestoreTripSyncService).
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        date_default_timezone_set(config('app.timezone', 'America/Bogota'));

        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        if (config('database.default') === 'sqlite') {
            try {
                DB::connection()->getPdo()->exec('PRAGMA busy_timeout = 15000');
                DB::connection()->getPdo()->exec('PRAGMA journal_mode = WAL');
            } catch (\Throwable $e) {
                // ignore if DB not ready yet
            }
        }

        view()->composer('*', function ($view)
        {
            
			$user = request()->user();
			$view->with('user', $user);

			$authRoutes = ['pasajero.login', 'conductor.login', 'empresa.login', 'empresa.afiliarse', 'empresa.afiliarse.ok', 'auth.register', 'pasajero.register'];
			$seg1 = request()->segment(1) ?? 'index';
			$seg2 = request()->segment(2) ?? 'index';
			$isAuthPage = request()->routeIs($authRoutes)
				|| ($seg1 === 'index' && in_array($seg2, ['login', 'register'], true));

			$layout = $isAuthPage ? 'layouts.auth' : 'layouts.app';
			if(request()->ajax()){
				$layout = "layouts.ajax";
			}
			$view->with('layout', $layout);


			$show_header = request()->show_header ?? true;
			$show_footer = request()->show_footer ?? true;
			$show_pagination = request()->show_pagination ?? true;

			$view->with('show_header', $show_header);
			$view->with('show_footer', $show_footer);
			$view->with('show_pagination', $show_pagination);

        });
    }
}
