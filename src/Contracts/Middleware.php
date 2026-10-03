<?php

namespace Jundayw\Proxy\Contracts;

use Closure;

interface Middleware
{
    public function __invoke($request, Closure $next);
}
