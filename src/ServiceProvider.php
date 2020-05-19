<?php
namespace Tangzhixing1218\Jwt;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(JWT::class, function(){
            return new JWT(config('services.jwt.iss'),config('services.jwt.exp_time'),config('services.jwt.key'));
        });

        $this->app->alias(JWT::class, 'jwt');
    }

    public function provides()
    {
        return [JWT::class, 'jwt'];
    }
}