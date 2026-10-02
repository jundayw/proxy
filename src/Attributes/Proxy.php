<?php

namespace Jundayw\Proxy\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Proxy
{
    public function __construct(
        public readonly array $interceptors = [],
    ) {
    }
}
