<?php

namespace Jundayw\Proxy\Concerns;

use Closure;
use Jundayw\Proxy\Attributes\Proxy;
use ReflectionClass;

trait HasProxiedMethods
{
    public function newProxyInstanceMethod(string $method, array $params = [])
    {
        $pipeline = array_reduce(
            array_reverse($this->getProxyInstanceMiddlewares($this)),
            fn(Closure $next, $middleware) => fn($request) => $middleware($request, $next),
            fn($request) => call_user_func_array([get_parent_class($this), $method], $request)
        );

        return $pipeline($params);
    }

    protected function getProxyInstanceMiddlewares(object|string $objectOrClass): array
    {
        $middlewares = [];
        $reflection  = new ReflectionClass($objectOrClass);
        $attributes  = $reflection->getAttributes(Proxy::class);

        foreach ($attributes as $attribute) {
            foreach ($attribute->newInstance()->getMiddlewares() as $middleware) {
                $middlewares[] = $middleware;
            }
        }

        if ($parent = $reflection->getParentClass()) {
            array_unshift(
                $middlewares,
                ...$this->getProxyInstanceMiddlewares($parent->getName())
            );
        }

        return $middlewares;
    }
}
