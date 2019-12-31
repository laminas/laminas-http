<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Client\TestAsset;

use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Http\Request;

class MockClient extends Client
{
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
