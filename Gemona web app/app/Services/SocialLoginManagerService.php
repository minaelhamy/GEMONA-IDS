<?php

namespace App\Services;


class SocialLoginManagerService
{

    public object $provider;

    public function provider(...$args) : static
    {
        $loginProvider = '';
        if (count($args) > 0) {
            $loginProvider = ucfirst(array_shift($args));
        }

        if (count($args) == 0) {
            $args = null;
        }

        $className     = 'App\\Http\\SocialProviders\\Providers\\' . $loginProvider;
        $this->provider = resolve($className);
        return $this;
    }

    public function getUrl()
    {

        return $this->provider->getUrl();
    }

    public function verifySocialLogin()
    {
        return $this->provider->verifySocialLogin();
        
    }

    public function status()
    {
        return $this->provider->status();
    }

}
