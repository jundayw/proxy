<?php

namespace Jundayw\Proxy;

class Configuration implements Contracts\Configuration
{
    public function __construct(
        protected array $config = [],
    ) {
    }

    /**
     * @param string $classFQN
     *
     * @return string
     */
    public function getProxyNamespace(string $classFQN = ''): string
    {
        $namespace = $this->config['proxyNamespace'] ?: 'Proxy\\__NAMESPACE__';

        return str_replace('__NAMESPACE__', $classFQN, $namespace);
    }

    /**
     * @param string $proxyNamespace
     *
     * @return static
     */
    public function setProxyNamespace(string $proxyNamespace): static
    {
        $this->config['proxyNamespace'] = $proxyNamespace;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetPath(): string
    {
        $path = $this->config['targetPath'] ?: '__PACKAGE__/proxy';

        return str_replace('__PACKAGE__', realpath(__DIR__.'/../'), $path);
    }

    /**
     * @param string $targetPath
     *
     * @return static
     */
    public function setTargetPath(string $targetPath): static
    {
        $this->config['targetPath'] = $targetPath;

        return $this;
    }

    public function getProxyTargetFilePath(string $classFQN = ''): string
    {
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $classFQN).'.php';

        return implode(DIRECTORY_SEPARATOR, [
            rtrim($this->getTargetPath(), '\\/'),
            ltrim($relative, '\\/'),
        ]);
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
    public function getInterfaces(): array
    {
        return $this->config['interfaces'] ?? [
            \Jundayw\Proxy\Contracts\Proxy::class,
        ];
    }

    /**
     * @param array $interfaces
     *
     * @return static
     */
    public function setInterfaces(array $interfaces): static
    {
        $this->config['interfaces'] = $interfaces;

        return $this;
    }

    public function addInterface(array|string $interfaces): static
    {
        if (is_string($interfaces)) {
            $interfaces = [$interfaces];
        }

        foreach ($interfaces as $interface) {
            $this->config['interfaces'][] = $interface;
        }

        return $this;
    }

    public function removeInterface(array|string $interfaces): static
    {
        if (is_string($interfaces)) {
            $interfaces = [$interfaces];
        }

        $this->config['interfaces'] = array_filter(
            $interfaces,
            fn(string $interface) => !in_array($interface, $this->config['interfaces'])
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
