<?php

namespace LaminasTest\Http\Client\TestAsset;

use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Http\Request;

class MockClient extends Client
{
    /** @var array<string, mixed> */
    public $config = [
        'maxredirects'    => 5,
        'strictredirects' => false,
        'useragent'       => 'Laminas_Http_Client',
        'timeout'         => 10,
        'adapter'         => Socket::class,
        'httpversion'     => Request::VERSION_11,
        'keepalive'       => false,
        'storeresponse'   => true,
        'strict'          => true,
        'outputstream'    => false,
        'encodecookies'   => true,
    ];
}
