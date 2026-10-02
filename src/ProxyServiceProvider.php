<?php

namespace Jundayw\Proxy;

use Illuminate\Support\ServiceProvider;

class ProxyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/proxy.php', 'proxy');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
        $this->app->bind(Contracts\Configuration::class, static function ($app) {
            return new Configuration($app['config']['proxy']);
        });
        $this->app->bind(Contracts\ProxyGenerator::class, static function ($app) {
            return $app->make(ProxyGenerator::class);
        });
        $this->app->bind(Contracts\ProxyManager::class, static function ($app) {
            return $app->make(ProxyManager::class);
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/proxy.php' => config_path('proxy.php'),
        ], 'config');
    }
}
