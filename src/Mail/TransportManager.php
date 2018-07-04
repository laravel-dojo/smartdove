<?php

namespace Meditate\SmartDove\Mail;

use Illuminate\Mail\TransportManager as IlluminateTransportManager;
use Meditate\SmartDove\Mail\Transport\SmartDoveTransport;

class TransportManager extends IlluminateTransportManager
{
    /**
     * Create an instance of the Mailgun Swift Transport driver.
     *
     * @return \Meditate\SmartDove\Mail\Transport\SmartDoveTransport
     */
    protected function createSmartdoveDriver()
    {
        $config = $this->app['config']->get('services.smartdove.mail', []);

        return new SmartDoveTransport(
            $this->guzzle($config),
            $config['token']
        );
    }
}
