<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $compiledViewPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'maharani-mobil-compiled-views';

        if (! is_dir($compiledViewPath)) {
            mkdir($compiledViewPath, 0777, true);
        }

        Config::set('view.compiled', $compiledViewPath);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
