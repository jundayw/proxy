<?php

namespace Jundayw\Proxy;

use Closure;
use Jundayw\Proxy\Contracts\Proxied;
use Laminas\Code\Reflection\ClassReflection;

class ProxyManager implements Contracts\ProxyManager
{
    public function __construct(
        protected Contracts\Configuration $configuration,
        protected Contracts\ProxyGenerator $proxyGenerator,
    ) {
    }

    /**
     * @return Contracts\Configuration
     */
    public function getConfiguration(): Contracts\Configuration
    {
        return $this->configuration;
    }

    /**
     * @return Contracts\ProxyGenerator
     */
    public function getProxyGenerator(): Contracts\ProxyGenerator
    {
        return $this->proxyGenerator;
    }

    public function create(object|string $className, Closure|null $classFactoryCallback = null): string|Proxied
    {
        $classReflection = new ClassReflection($className);
        $proxyClassName  = $this->generate($classReflection);

        if (is_null($classFactoryCallback)) {
            return $proxyClassName;
        }

        return call_user_func_array($classFactoryCallback, [
            $proxyClassName,
            $proxyClassName === $classReflection->getName(),
        ]);
    }

    protected function hasProxyInterface(ClassReflection $classReflection): bool
    {
        return array_some($classReflection->getInterfaces(), function (ClassReflection $reflection): bool {
            return array_some($this->getConfiguration()->getProxyInterfaces(), function ($interfaceName) use ($reflection) {
                return is_a($reflection->getName(), $interfaceName, true);
            });
        });
    }

    protected function hasAttribute(ClassReflection $classReflection): bool
    {
        return array_some($classReflection->getAttributes(), function (\ReflectionAttribute $reflection): bool {
            return array_some($this->getConfiguration()->getAttributes(), function ($attributeName) use ($reflection) {
                return is_a($reflection->getName(), $attributeName, true);
            });
        });
    }

    protected function hasIgnoreAttribute(ClassReflection $classReflection): bool
    {
        return array_some($classReflection->getAttributes(), function (\ReflectionAttribute $reflection): bool {
            return array_some($this->getConfiguration()->getIgnoreAttributes(), function ($attributeName) use ($reflection) {
                return is_a($reflection->getName(), $attributeName, true);
            });
        });
    }

    protected function isProxyable(ClassReflection $classReflection): bool
    {
        if ($this->getConfiguration()->enabled() === false) {
            return false;
        }

        if ($this->hasAttribute($classReflection)) {
            return true;
        }

        if ($this->hasIgnoreAttribute($classReflection)) {
            return false;
        }

        if ($this->hasProxyInterface($classReflection)) {
            return true;
        }

        return false;
    }

    protected function generate(ClassReflection $classReflection): string
    {
        if ($this->isProxyable($classReflection) === false) {
            return $classReflection->getName();
        }

        $proxyClassName = $this->getConfiguration()->getProxyNamespaceName(
            $className = $classReflection->getName(),
            $inNamespace = $classReflection->inNamespace()
        );

        $proxyClassName = $this->saveProxyClassContent(
            $this->getConfiguration()->getProxyNamespacePath(
                $inNamespace ? $className : $this->getConfiguration()->getProxyNamespaceName(
                    $className,
                    false
                ),
                $inNamespace
            ),
            $this->getProxyGenerator()->generate($className)
        ) ? $proxyClassName : $className;

        return class_exists($proxyClassName) ? $proxyClassName : $className;
    }

    public function saveProxyClassContent($filePath, $contents, $flags = 0): false|int
    {
        $dir = dirname($filePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return file_put_contents($filePath, $contents, $flags);
    }

}
