<?php

namespace Jundayw\Proxy\Contracts;

interface Configuration
{
    public function getProxyNamespace(string $classFQN = ''): string;

    public function setProxyNamespace(string $proxyNamespace): static;

    public function getTargetPath(): string;

    public function setTargetPath(string $targetPath): static;

    public function getProxyTargetFilePath(string $classFQN = ''): string;

    public function getMiddlewares(): array;

    public function setMiddlewares(array $middlewares): static;

    public function addMiddleware(array|string $middlewares): static;

    public function removeMiddleware(array|string $middlewares): static;

    public function getInterfaces(): array;

    public function setInterfaces(array $interfaces): static;

    public function addInterface(array|string $interfaces): static;

    public function removeInterface(array|string $interfaces): static;

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
