<?php

namespace Meditate\SmartDove;

use Illuminate\Support\ServiceProvider;
use Meditate\SmartDove\SMS\Client;

class SmartDoveServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
        	$token = $this->app['config']->get('services.smartdove.sms.token', '');

        	return new Client($token);
        });
    }
}
