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
    protected $batchApiEndpoint = 'https://api.smartdove.net/index.php?r=smsApi/SendMultiSms';

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
     * @var array
     */
    protected $receivers = [];

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
     * @param mixed $to
     * @return Client
     */
    public function to($to): Client
    {
        $this->receivers = array_merge($this->receivers, is_array($to) ? $to : func_get_args());

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
        if (count($this->receivers) > 1 || ! is_null($params)) {
            $apiEndPoint = $this->batchApiEndpoint;
            $apiParams = $this->buildBatchParams($params);
        } else {
            $apiEndPoint = $this->apiEndpoint;
            $apiParams = $this->buildParams($params);
        }

        $request = $this->messageFactory->createRequest(
            'POST',
            $apiEndPoint,
            ['Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'],
            $apiParams
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
            'phone_number' => $this->receivers[0],
            'content' => $this->text,
        ];

        if (! is_null($params)) {
            $config = array_merge($config, $params);
        }

        return http_build_query($config);
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildBatchParams(array $params = null): string
    {
        $config = [
            'token' => $this->token,
        ];

        $data = [];
        foreach ($this->receivers as $receiver) {
            $data[] = [
                'id' => str_random(7),
                'phone_number' => $receiver,
                'content' => $this->text,
            ];
        }

        if (! is_null($params)) {
            if (is_array($params['phone_number'])) {
                foreach ($params['phone_number'] as $number) {
                    $data[] = [
                        'id' => str_random(7),
                        'phone_number' => $number,
                        'content' => $params['content'],
                    ];
                }
            } else {
                $data[] = [
                    'id' => str_random(7),
                    'phone_number' => $params['phone_number'],
                    'content' => $params['content'],
                ];
            }
        }

        $config['data'] = json_encode($data);

        return http_build_query($config);
    }
}
