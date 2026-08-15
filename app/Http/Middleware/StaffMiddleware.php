<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ) {
        $user = $request->user();

        abort_unless(
            $user && $user->isStaff(),
            403,
            'Bạn không có quyền truy cập khu vực nhân viên.'
        );

        return $next($request);
    }
}