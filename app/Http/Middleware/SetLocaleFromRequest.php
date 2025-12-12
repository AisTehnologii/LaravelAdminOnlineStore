<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    protected array $availableLocales = ['en', 'ru', 'ro'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if ($request->has('lang')) {
            $locale = $request->get('lang');
        }

        if (! $locale) {
            $locale = 'en';
        }

        if (! in_array($locale, $this->availableLocales, true)) {
            $locale = 'en';
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return $next($request);
    }
}
