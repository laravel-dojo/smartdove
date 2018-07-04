<?php

namespace Meditate\SmartDove\SMS;

use Illuminate\Notifications\Notification;

class SmartDoveChannel
{
    /**
     * $client.
     *
     * @var \Meditate\SmartDove\SMS\Client
     */
    protected $client;

    /**
     * __construct.
     *
     * @param \Meditate\SmartDove\SMS\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \Meditate\SmartDove\SMS\SmartDoveMessage
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('smartdove')) {
            return;
        }

        $message = $notification->toSmartDove($notifiable);

        if (is_string($message)) {
            $message = new SmartDoveMessage($message);
        }

        return $this->client->send([
            'phone_number' => $to,
            'content' => trim($message->content),
        ]);
    }
}
