<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (Auth::check()) {
            $userRole = Auth::user()->utype;
            if (in_array($userRole, $roles)) {
                return $next($request);
            }
        }

        session()->flush();
        return redirect()->route('login');
    }
}
