<?php

namespace Jundayw\Proxy;

use Laminas\Code\Generator\ClassGenerator;
use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\PromotedParameterGenerator;
use Laminas\Code\Reflection\ClassReflection;
use Laminas\Code\Reflection\MethodReflection;
use ReflectionAttribute;
use ReflectionNamedType;

class ProxyGenerator implements Contracts\ProxyGenerator
{
    public function __construct(
        protected Contracts\Configuration $configuration,
    ) {
    }

    public function getConfiguration(): Contracts\Configuration
    {
        return $this->configuration;
    }

    public function setExtendedClass(
        ClassReflection $classReflection,
        ClassGenerator $classGenerator,
    ): static {
        $classGenerator->addUse(
            $classReflection->getName(),
            $useAlias = "{$classReflection->getShortName()}Alias"
        );
        $classGenerator->setExtendedClass($useAlias);

        return $this;
    }

    public function setNamespaceName(
        ClassReflection $classReflection,
        ClassGenerator $classGenerator,
    ): static {
        if ($classReflection->inNamespace()) {
            $classGenerator->setNamespaceName(
                $this->getConfiguration()->getProxyNamespaceName(
                    $classReflection->getNamespaceName(), !false
                )
            );
        } else {
            $classGenerator->setNamespaceName(
                $this->getConfiguration()->getNamespaceName(false)
            );
        }

        return $this;
    }

    protected function isProxyRelated(
        ClassReflection $classReflection,
    ): bool {
        foreach ($this->getConfiguration()->getProxiedInterfaces() as $interface) {
            if (is_a($classReflection->getName(), $interface, true)) {
                return true;
            }
        }

        foreach ($this->getConfiguration()->getProxyInterfaces() as $interface) {
            if (is_a($classReflection->getName(), $interface, true)) {
                return true;
            }
        }

        return false;
    }

    public function setImplementedInterfaces(
        ClassReflection $classReflection,
        ClassGenerator $classGenerator,
    ): static {
        $interfaceNames = $this->getConfiguration()->getProxiedInterfaces();

        foreach ($classReflection->getInterfaces() as $interface) {
            if (!$this->isProxyRelated($interface)) {
                $interfaceNames[] = $interface->getName();
            }
        }

        $classGenerator->setImplementedInterfaces($interfaceNames);

        return $this;
    }

    public function addTraits(
        ClassReflection $classReflection,
        ClassGenerator $classGenerator,
    ): static {
        $traits = [];

        do {
            foreach ($classReflection->getTraits() as $trait) {
                $traits[$trait->getName()] = $trait;
            }
        } while ($classReflection = $classReflection->getParentClass());

        foreach ($this->getConfiguration()->getProxiedTraits() as $trait) {
            if (!in_array($trait, $traits)) {
                $classGenerator->addTrait(str_starts_with($trait, '\\') ? $trait : '\\'.$trait);
            }
        }

        return $this;
    }

    protected function hasReturnVoidType(
        MethodReflection $methodReflection,
        bool $tentative = false,
    ): bool {
        $type = $tentative ? $methodReflection->getTentativeReturnType() : $methodReflection->getReturnType();

        if (!($type instanceof ReflectionNamedType)) {
            return $tentative ? false : $this->hasReturnVoidType($methodReflection, !$tentative);
        }

        return in_array($type->getName(), ['void', 'never']);
    }

    protected function hasReturnType(
        MethodReflection $methodReflection,
    ): bool {
        foreach (token_get_all("<?php {$methodReflection->getBody()} ?>", TOKEN_PARSE) as $token) {
            if (is_array($token) && token_name($token[0]) == 'T_RETURN') {
                return true;
            }
        }
        return false;
    }

    protected function isProxyAttributeRelated(array $attributes): bool
    {
        foreach ($this->getConfiguration()->getIgnoreAttributes() as $attribute) {
            if (array_filter(
                $attributes,
                function (ReflectionAttribute $reflectionAttribute) use ($attribute) {
                    return is_a($reflectionAttribute->getName(), $attribute, true);
                }
            )) {
                return false;
            }
        }

        return true;
    }

    protected function shouldProxyMethod(
        ClassReflection $classReflection,
        MethodReflection $methodReflection,
    ): bool {
        return $this->isProxyAttributeRelated($methodReflection->getAttributes())
            && $methodReflection->getDeclaringClass()->getName() == $classReflection->getName()
            && $methodReflection->getFileName() == $classReflection->getFileName()
            && $methodReflection->isPublic()
            && $methodReflection->isStatic() === false
            && $methodReflection->isFinal() === false;
    }

    public function addMethods(
        ClassReflection $classReflection,
        ClassGenerator $classGenerator,
    ): static {
        $methods = [];

        foreach ($classReflection->getMethods() as $reflectionMethod) {
            if ($this->shouldProxyMethod($classReflection, $reflectionMethod)) {
                $method = MethodGenerator::copyMethodSignature($reflectionMethod);

                $method->setSourceContent($reflectionMethod->getContents());
                $method->setSourceDirty(false);

                $body = "\$this->newProxyInstanceMethod(__FUNCTION__, func_get_args());";
                $method->setBody(
                    $this->hasReturnVoidType($reflectionMethod) || !$this->hasReturnType($reflectionMethod)
                        ? $body
                        : "return {$body}"
                );

                if ($reflectionMethod->isConstructor()) {
                    foreach ($method->getParameters() as $parameter) {
                        if ($parameter instanceof PromotedParameterGenerator) {
                            $classGenerator->removeProperty($parameter->getName());
                        }
                    }
                    continue;
                }

                $methods[] = $method;
            }
        }

        $classGenerator->addMethods($methods);

        return $this;
    }

    protected function toString(
        ClassGenerator $classGenerator,
    ): string {
        return implode(PHP_EOL.PHP_EOL, [
            "<?php",
            $classGenerator->generate(),
        ]);
    }

    public function generate(object|string $instance): string
    {
        $classReflection = new ClassReflection($instance);
        $classGenerator  = new ClassGenerator($classReflection->getName());
        $classGenerator->setSourceContent($classGenerator->getSourceContent());
        $classGenerator->setSourceDirty(false);

        $classGenerator->setAbstract($classReflection->isAbstract());
        $classGenerator->setFinal($classReflection->isFinal());
        $classGenerator->setReadonly($classReflection->isReadonly());

        return $this->setExtendedClass($classReflection, $classGenerator)
            ->setNamespaceName($classReflection, $classGenerator)
            ->setImplementedInterfaces($classReflection, $classGenerator)
            ->addTraits($classReflection, $classGenerator)
            ->addMethods($classReflection, $classGenerator)
            ->toString($classGenerator);
    }

}
