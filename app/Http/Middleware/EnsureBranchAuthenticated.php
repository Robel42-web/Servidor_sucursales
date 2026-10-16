<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAuthenticated
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (session('branch_authenticated') !== true) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}