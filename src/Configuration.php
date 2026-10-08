<?php

namespace Jundayw\Proxy;

class Configuration implements Contracts\Configuration
{
    public function __construct(
        protected array $config = [],
    ) {
    }

    public function enabled(): bool
    {
        return $this->config['enabled'] ?? true;
    }

    public function enable(bool $enable): static
    {
        $this->config['enabled'] = $enable;

        return $this;
    }

    /**
     * @param string $classFQN
     * @param bool   $inNamespace
     *
     * @return string
     */
    public function getProxyNamespaceName(string $classFQN = '', bool $inNamespace = true): string
    {
        return str_replace(
            '__NAMESPACE__',
            $classFQN,
            "{$this->getNamespaceName($inNamespace)}\\__NAMESPACE__"
        );
    }

    /**
     * @param bool $inNamespace
     *
     * @return string
     */
    public function getNamespaceName(bool $inNamespace = true): string
    {
        $psr0 = $this->config['PSR-0'] ?? 'Proxy\\';
        $psr4 = $this->config['PSR-4'] ?? 'Proxy\\';

        return trim($inNamespace ? $psr4 : $psr0, '\\');
    }

    /**
     * @param string $namespace
     * @param bool   $inNamespace
     *
     * @return static
     */
    public function setNamespaceName(string $namespace, bool $inNamespace = true): static
    {
        if ($inNamespace) {
            $this->config['PSR-4'] = $namespace;
        } else {
            $this->config['PSR-0'] = $namespace;
        }

        return $this;
    }

    public function getProxyNamespacePath(string $classFQN = '', bool $inNamespace = true): string
    {
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $classFQN).'.php';

        return implode(DIRECTORY_SEPARATOR, [
            rtrim($this->getNamespacePath($inNamespace), '\\/'),
            ltrim($relative, '\\/'),
        ]);
    }

    /**
     * @param bool $inNamespace
     *
     * @return string
     */
    public function getNamespacePath(bool $inNamespace = true): string
    {
        $path0 = $this->config['path']['PSR-0'] ?? '__PACKAGE__/PSR-0';
        $path4 = $this->config['path']['PSR-4'] ?? '__PACKAGE__/PSR-4';

        return str_replace('__PACKAGE__', realpath(__DIR__.'/../'), $inNamespace ? $path4 : $path0);
    }

    /**
     * @param string $path
     * @param bool   $inNamespace
     *
     * @return static
     */
    public function setNamespacePath(string $path, bool $inNamespace = true): static
    {
        $this->config['path'] ??= [];

        if ($inNamespace) {
            $this->config['path']['PSR-4'] = $path;
        } else {
            $this->config['path']['PSR-0'] = $path;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->config['middlewares'] ?? [];
    }

    /**
     * @param array $middlewares
     *
     * @return static
     */
    public function setMiddlewares(array $middlewares): static
    {
        $this->config['middlewares'] = $middlewares;

        return $this;
    }

    public function addMiddleware(array|string $middlewares): static
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }

        $this->config['middlewares'] ??= [];

        foreach ($middlewares as $middleware) {
            $this->config['middlewares'][] = $middleware;
        }

        return $this;
    }

    public function removeMiddleware(array|string $middlewares): static
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }

        $this->config['middlewares'] = array_filter(
            $middlewares,
            fn(string $middleware) => !in_array($middleware, $this->config['middlewares'])
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getProxyInterfaces(): array
    {
        return $this->config['proxy']['interfaces'] ?? [
            \Jundayw\Proxy\Contracts\Proxy::class,
        ];
    }

    /**
     * @param array $interfaces
     *
     * @return static
     */
    public function setProxyInterfaces(array $interfaces): static
    {
        $this->config['proxy']               ??= [];
        $this->config['proxy']['interfaces'] = $interfaces;

        return $this;
    }

    public function addProxyInterface(array|string $interfaces): static
    {
        if (is_string($interfaces)) {
            $interfaces = [$interfaces];
        }

        $this->config['proxy']               ??= [];
        $this->config['proxy']['interfaces'] ??= [];

        foreach ($interfaces as $interface) {
            $this->config['proxy']['interfaces'][] = $interface;
        }

        return $this;
    }

    public function removeProxyInterface(array|string $interfaces): static
    {
        if (is_string($interfaces)) {
            $interfaces = [$interfaces];
        }

        $this->config['proxy']               ??= [];
        $this->config['proxy']['interfaces'] = array_filter(
            $interfaces,
            fn(string $interface) => !in_array($interface, $this->config['proxy']['interfaces'])
        );

        return $this;
    }

    public function isProxied(string $className): bool
    {
        return count(array_filter(
            $this->getProxiedInterfaces(),
            fn($interface) => $interface === $className || is_subclass_of($interface, $className)
        ));
    }

    /**
     * @return array
     */
    public function getProxiedInterfaces(): array
    {
        return $this->config['proxied']['interfaces'] ?? [
            \Jundayw\Proxy\Contracts\Proxied::class,
        ];
    }

    /**
     * @param array $interfaces
     *
     * @return static
     */
    public function setProxiedInterfaces(array $interfaces): static
    {
        $this->config['proxied']               ??= [];
        $this->config['proxied']['interfaces'] = $interfaces;

        return $this;
    }

    public function addProxiedInterface(array|string $interfaces): static
    {
        if (is_string($interfaces)) {
            $interfaces = [$interfaces];
        }

        $this->config['proxied']               ??= [];
        $this->config['proxied']['interfaces'] ??= [];

        foreach ($interfaces as $interface) {
            $this->config['proxied']['interfaces'][] = $interface;
        }

        return $this;
    }

    public function removeProxiedInterface(array|string $interfaces): static
    {
        if (is_string($interfaces)) {
            $interfaces = [$interfaces];
        }

        $this->config['proxied']               ??= [];
        $this->config['proxied']['interfaces'] = array_filter(
            $interfaces,
            fn(string $interface) => !in_array($interface, $this->config['proxied']['interfaces'])
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getProxiedTraits(): array
    {
        return $this->config['proxied']['traits'] ?? [
            \Jundayw\Proxy\Concerns\HasProxiedMethods::class,
        ];
    }

    /**
     * @param array $traits
     *
     * @return static
     */
    public function setProxiedTraits(array $traits): static
    {
        $this->config['proxied']           ??= [];
        $this->config['proxied']['traits'] = $traits;

        return $this;
    }

    public function addProxiedTrait(array|string $traits): static
    {
        if (is_string($traits)) {
            $traits = [$traits];
        }

        $this->config['proxied']           ??= [];
        $this->config['proxied']['traits'] ??= [];

        foreach ($traits as $trait) {
            $this->config['proxied']['traits'][] = $trait;
        }

        return $this;
    }

    public function removeProxiedTrait(array|string $traits): static
    {
        if (is_string($traits)) {
            $traits = [$traits];
        }

        $this->config['proxied']           ??= [];
        $this->config['proxied']['traits'] = array_filter(
            $traits,
            fn(string $trait) => !in_array($trait, $this->config['proxied']['traits'])
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->config['attributes'] ?? [
            \Jundayw\Proxy\Attributes\Proxy::class,
        ];
    }

    /**
     * @param array $attributes
     *
     * @return static
     */
    public function setAttributes(array $attributes): static
    {
        $this->config['attributes'] = $attributes;

        return $this;
    }

    public function addAttribute(array|string $attributes): static
    {
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        $this->config['attributes'] ??= [];

        foreach ($attributes as $attribute) {
            $this->config['attributes'][] = $attribute;
        }

        return $this;
    }

    public function removeAttribute(array|string $attributes): static
    {
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        $this->config['attributes'] = array_filter(
            $attributes,
            fn(string $attribute) => !in_array($attribute, $this->config['attributes'])
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getIgnoreAttributes(): array
    {
        return $this->config['ignoreAttributes'] ?? [
            \Jundayw\Proxy\Attributes\ProxyIgnore::class,
            \Jundayw\Proxy\Attributes\IgnoreProxy::class,
        ];
    }

    /**
     * @param array $attributes
     *
     * @return static
     */
    public function setIgnoreAttributes(array $attributes): static
    {
        $this->config['ignoreAttributes'] = $attributes;

        return $this;
    }

    public function addIgnoreAttribute(array|string $attributes): static
    {
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        $this->config['ignoreAttributes'] ??= [];

        foreach ($attributes as $attribute) {
            $this->config['ignoreAttributes'][] = $attribute;
        }

        return $this;
    }

    public function removeIgnoreAttribute(array|string $attributes): static
    {
        if (is_string($attributes)) {
            $attributes = [$attributes];
        }

        $this->config['ignoreAttributes'] = array_filter(
            $attributes,
            fn(string $attribute) => !in_array($attribute, $this->config['ignoreAttributes'])
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getClasses(): array
    {
        return $this->config['classes'] ?? [];
    }

    /**
     * @param array $classes
     *
     * @return static
     */
    public function setClasses(array $classes): static
    {
        $this->config['classes'] = $classes;

        return $this;
    }

    public function addClass(array|string $classes): static
    {
        if (is_string($classes)) {
            $classes = [$classes];
        }

        $this->config['classes'] ??= [];

        foreach ($classes as $class) {
            $this->config['classes'][] = $class;
        }

        return $this;
    }

    public function removeClass(array|string $classes): static
    {
        if (is_string($classes)) {
            $classes = [$classes];
        }

        $this->config['classes'] = array_filter(
            $classes,
            fn(string $class) => !in_array($class, $this->config['classes'])
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return $this->config['directories'] ?? [];
    }

    /**
     * @param array $directories
     *
     * @return static
     */
    public function setDirectories(array $directories): static
    {
        $this->config['directories'] = $directories;

        return $this;
    }

    public function addDirectory(array|string $directories): static
    {
        if (is_string($directories)) {
            $directories = [$directories];
        }

        $this->config['directories'] ??= [];

        foreach ($directories as $directory) {
            $this->config['directories'][] = $directory;
        }

        return $this;
    }

    public function removeDirectory(array|string $directories): static
    {
        if (is_string($directories)) {
            $directories = [$directories];
        }

        $this->config['directories'] = array_filter(
            $directories,
            fn(string $directory) => !in_array($directory, $this->config['directories'])
        );

        return $this;
    }
}
