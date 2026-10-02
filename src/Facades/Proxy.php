<?php

namespace Jundayw\Proxy\Facades;

use Illuminate\Support\Facades\Facade;
use Jundayw\Proxy\Contracts\ProxyManager;

class Proxy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ProxyManager::class;
    }
}
