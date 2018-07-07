<?php

namespace Meditate\SmartDove\Mail\Transport;

use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;
use GuzzleHttp\ClientInterface;

class SmartDoveTransport extends Transport
{
    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The Mailgun API token.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new Mailgun transport instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $token
     * @return void
     */
    public function __construct(ClientInterface $client, $token)
    {
        $this->token = $token;
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $from = $this->getFrom($message);

        $to = $this->getTo($message);

        $message->setBcc([]);

        $message->getHeaders()->addTextHeader(
            'content-type', 'application/x-www-form-urlencoded'
        );

        $this->client->post('https://api.smartdove.net/index.php?r=api/SendMail', [
            'form_params' => $this->payload($message, $from, $to),
        ]);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the HTTP payload for sending the Mailgun message.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @param  array  $from
     * @param  array  $to
     * @return array
     */
    protected function payload(Swift_Mime_SimpleMessage $message, $from, $to)
    {
        return [
            'token' => $this->getToken(),
            'data' => json_encode([
                'From' => $from,
                'To' => $to,
                'Subject' => $message->getSubject(),
                'HTML' => $message->getBody(),
            ]),
        ];
    }

    /**
     * Get the "from" payload field for the API request.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function getFrom(Swift_Mime_SimpleMessage $message)
    {
        $from = $message->getFrom();

        $form_email = key($from);
        $form_name = $from[$form_email] ?? null;

        return [
            'Email' => $form_email,
            'Name' => $form_name,
        ];
    }

    /**
     * Get the "to" payload field for the API request.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($this->allContacts($message))->map(function ($display, $address) {
            return ['Email' => $address];
        })->values()->toArray();
    }

    /**
     * Get all of the contacts for the message.
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array) $message->getTo(), (array) $message->getCc(), (array) $message->getBcc()
        );
    }

    /**
     * Get the API token being used by the transport.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the API token being used by the transport.
     *
     * @param  string  $token
     * @return string
     */
    public function setToken($token)
    {
        return $this->token = $token;
    }
}
