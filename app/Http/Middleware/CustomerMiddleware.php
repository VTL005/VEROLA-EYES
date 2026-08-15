<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ) {
        $user = $request->user();

        abort_unless(
            $user && $user->isCustomer(),
            403,
            'Chức năng này chỉ dành cho khách hàng.'
        );

        return $next($request);
    }
}