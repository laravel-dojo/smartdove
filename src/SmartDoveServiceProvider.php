<?php

namespace Meditate\SmartDove;

use Illuminate\Mail\MailServiceProvider;
use Meditate\SmartDove\Mail\TransportManager;

class SmartDoveServiceProvider extends MailServiceProvider
{
    public function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new TransportManager($app);
        });
    }
}
