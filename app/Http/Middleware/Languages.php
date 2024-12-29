<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Languages
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, \Closure $next)
    {
        $currentlanguage = app()->getLocale();

        if (Session::has('lang')) {
            $newLanguage = Session::get('lang');
            if ($newLanguage != $currentlanguage) {
                $currentlanguage = $newLanguage;
                App::setLocale($currentlanguage);
            }
        }

        return $next($request);
    }
}
