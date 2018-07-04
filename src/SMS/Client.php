<?php

namespace Meditate\SmartDove\SMS;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

class Client
{
    /**
     * @var string
     */
    protected $apiEndpoint = 'https://api.smartdove.net/index.php?r=smsApi/SendOneSms';

    /**
     * @var string
     */
    protected $token;

    /**
     * @var \Http\Client\HttpClient
     */
    protected $httpClient;

    /**
     * @var \Http\Message\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $token
     * @param \Http\Client\HttpClient $httpClient
     * @param \Http\Message\MessageFactory $messageFactory
     */
    public function __construct(string $token, HttpClient $httpClient = null, MessageFactory $messageFactory = null)
    {
        $this->token = $token;
        $this->httpClient = $httpClient ? : HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ? : MessageFactoryDiscovery::find();
    }

    /**
     * @param string $to
     * @return Client
     */
    public function to(string $to): Client
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $text
     * @return Client
     */
    public function text(string $text): Client
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param array $params
     * @return array
     */
    public function send($params = null): array
    {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->apiEndpoint,
            ['Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'],
            $this->buildParams($params)
        );

        $response = $this->httpClient->sendRequest($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildParams(array $params = null): string
    {
        $config = [
            'token' => $this->token,
            'phone_number' => $this->to,
            'content' => $this->text,
            'campaign_id' => '',
        ];

        if (! is_null($params)) {
            $config = array_merge($config, $params);
        }

        return http_build_query($config);
    }
}