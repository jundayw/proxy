<?php

namespace Jundayw\Proxy\Attributes;

use Attribute;
use Jundayw\Proxy\Contracts\Middleware;

#[Attribute(Attribute::TARGET_CLASS)]
final class Proxy
{
    /**
     * @param Middleware[] $middlewares
     */
    public function __construct(
        protected array $middlewares = [],
    ) {
    }

    public function getMiddlewares(): array
    {
        return array_filter(
            $this->middlewares,
            fn($middleware) => $middleware instanceof Middleware
        );
    }
}
