<?php

namespace LightMVC\Core;

use LightMVC\Core\Container\Container;

class Application extends Container
{
    protected $providers = [];

    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $provider = new $provider($this);

            if (!$provider instanceof ServiceProvider) {
                throw new \Exception('Invalid service provider ' . $provider);
            }

            $provider->register();
            $this->providers[] = $provider;
        }
    }

    public function boot()
    {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }
}
