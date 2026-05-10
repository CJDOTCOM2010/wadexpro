<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->route('lang');
        $country = $request->route('country');

        if ($lang) {
            App::setLocale($lang);
        }

        // Share the country and lang with URL generator for route redirection helper consistency
        URL::defaults([
            'country' => $country,
            'lang' => $lang,
        ]);

        return $next($request);
    }
}
