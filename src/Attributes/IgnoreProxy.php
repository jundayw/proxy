<?php

namespace Jundayw\Proxy\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class IgnoreProxy extends ProxyIgnore
{

}
