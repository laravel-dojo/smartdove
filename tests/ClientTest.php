<?php

namespace Meditate\SmartDove\SMS\Tests;

use Dotenv\Dotenv;
use Meditate\SmartDove\SMS\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected $token;
    protected $params;

    protected function setUp()
    {
        $dotenv = new Dotenv(__DIR__);
        $dotenv->load();

        $this->token = env('SMS_TOKEN');
        $this->params = [
            'to' => env('SMS_TO'),
            'text' => env('SMS_TEXT'),
        ];
    }

    public function testSendSmsUsingFluent()
    {
        // arrange
        $client = new Client($this->token);

        // act
        $response = $client->to($this->params['to'])->text($this->params['text'])->send();

        // assert
        $this->assertInternalType('array', $response);
    }

    public function testSendSmsUsingSend()
    {
        // arrange
        $client = new Client($this->token);

        // act
        $response = $client->send([
            'phone_number' => $this->params['to'],
            'content' => $this->params['text'],
        ]);

        // assert
        $this->assertInternalType('array', $response);
    }
}