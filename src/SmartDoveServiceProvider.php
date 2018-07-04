<?php

namespace Meditate\SmartDove;

use Illuminate\Mail\MailServiceProvider;
use Illuminate\Mail\Markdown;
use Meditate\SmartDove\Mail\TransportManager;
use Meditate\SmartDove\SMS\Client;

class SmartDoveServiceProvider extends MailServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $token = $this->app['config']->get('services.smartdove.sms.token', '');

            return new Client($token);
        });
    }

    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    public function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new TransportManager($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'mailer',
            'swift.mailer',
            'swift.transport',
            Markdown::class,
            Client::class,
        ];
    }
}
