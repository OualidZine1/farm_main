<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add any routes that should be excluded from CSRF verification
    ];
    
    public function handle($request, \Closure $next)
    {
        // Ensure session is started before any view is rendered
        if ($request->hasSession() && !$request->session()->isStarted()) {
            $request->session()->start();
        }
        
        return parent::handle($request, $next);
    }
}
