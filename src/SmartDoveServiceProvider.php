<?php

namespace Meditate\SmartDove;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Meditate\SmartDove\Mail\TransportManager;

class SmartDoveServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new TransportManager($app);
        });
    }
}
