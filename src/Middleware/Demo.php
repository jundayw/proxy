<?php

namespace Jundayw\Proxy\Middleware;

use Closure;
use Jundayw\Proxy\Contracts\Middleware;

class Demo implements Middleware
{
    public function __invoke($request, Closure $next)
    {
        return $next($request);
    }
}
