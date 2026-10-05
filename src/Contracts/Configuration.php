<?php

namespace Jundayw\Proxy\Contracts;

interface Configuration
{
    public function enabled(): bool;

    public function enable(bool $enable): static;

    public function getProxyNamespaceName(string $classFQN = '', bool $inNamespace = true): string;

    public function getNamespaceName(bool $inNamespace = true): string;

    public function setNamespaceName(string $namespace, bool $inNamespace = true): static;

    public function getProxyNamespacePath(string $classFQN = '', bool $inNamespace = true): string;

    public function getNamespacePath(bool $inNamespace = true): string;

    public function setNamespacePath(string $path, bool $inNamespace = true): static;

    public function getMiddlewares(): array;

    public function setMiddlewares(array $middlewares): static;

    public function addMiddleware(array|string $middlewares): static;

    public function removeMiddleware(array|string $middlewares): static;

    public function getProxyInterfaces(): array;

    public function setProxyInterfaces(array $interfaces): static;

    public function addProxyInterface(array|string $interfaces): static;

    public function removeProxyInterface(array|string $interfaces): static;

    public function getProxiedInterfaces(): array;

    public function setProxiedInterfaces(array $interfaces): static;

    public function addProxiedInterface(array|string $interfaces): static;

    public function removeProxiedInterface(array|string $interfaces): static;

    public function getProxiedTraits(): array;

    public function setProxiedTraits(array $traits): static;

    public function addProxiedTrait(array|string $traits): static;

    public function removeProxiedTrait(array|string $traits): static;

    public function getAttributes(): array;

    public function setAttributes(array $attributes): static;

    public function addAttribute(array|string $attributes): static;

    public function removeAttribute(array|string $attributes): static;

    public function getIgnoreAttributes(): array;

    public function setIgnoreAttributes(array $attributes): static;

    public function addIgnoreAttribute(array|string $attributes): static;

    public function removeIgnoreAttribute(array|string $attributes): static;

    public function getClasses(): array;

    public function setClasses(array $classes): static;

    public function addClass(array|string $classes): static;

    public function removeClass(array|string $classes): static;

    public function getDirectories(): array;

    public function setDirectories(array $directories): static;

    public function addDirectory(array|string $directories): static;

    public function removeDirectory(array|string $directories): static;

}
