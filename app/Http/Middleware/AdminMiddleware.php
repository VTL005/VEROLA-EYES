<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ) {
        $user = $request->user();

        abort_unless(
            $user && $user->isAdmin(),
            403,
            'Bạn không có quyền truy cập khu vực quản trị.'
        );

        return $next($request);
    }
}