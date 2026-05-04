<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Menentukan locale aplikasi berdasarkan session user pada setiap request web.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['id', 'en'];
        $locale = $request->session()->get('locale', config('app.locale'));

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}

