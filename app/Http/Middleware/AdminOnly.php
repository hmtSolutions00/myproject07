<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_admin) {
            // kalau belum login => ke login admin
            if(!$user){
                return redirect()->route('admin.login');
            }

            // kalau login tapi bukan admin => forbidden
            abort(403, 'Only admin can access this page.');
        }

        return $next($request);
    }
}
