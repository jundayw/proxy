<?php

namespace Jundayw\Proxy\Contracts;

interface Proxied
{
    public function newProxyInstanceMethod(string $method, array $params = []);

}
