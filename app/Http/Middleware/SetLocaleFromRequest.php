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
        // 1) Если передали ?lang=xx — используем его
        $locale = $request->query('lang');

        // 2) Иначе — берем из session
        if (! $locale) {
            $locale = session('locale');
        }

        // 3) Если всё равно пусто — дефолт из конфига
        if (! $locale) {
            $locale = config('app.locale', 'en');
        }

        // 4) Валидация
        if (! in_array($locale, $this->availableLocales, true)) {
            $locale = config('app.locale', 'en');
        }

        // 5) Сохраняем только если изменился (чтобы не дергать session лишний раз)
        if (session('locale') !== $locale) {
            session(['locale' => $locale]);
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
