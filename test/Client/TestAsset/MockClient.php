<?php

namespace LaminasTest\Http\Client\TestAsset;

use Laminas\Http\Request;

class MockClient extends \Laminas\Http\Client
{
    public $config = [
        'maxredirects'    => 5,
        'strictredirects' => false,
        'useragent'       => 'Laminas_Http_Client',
        'timeout'         => 10,
        'adapter'         => 'Laminas\\Http\\Client\\Adapter\\Socket',
        'httpversion'     => Request::VERSION_11,
        'keepalive'       => false,
        'storeresponse'   => true,
        'strict'          => true,
        'outputstream'   => false,
        'encodecookies'   => true,
    ];
}
