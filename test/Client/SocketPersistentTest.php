<?php

namespace LaminasTest\Http\Client;

/**
 * This Testsuite includes all Laminas_Http_Client that require a working web
 * server to perform. It was designed to be extendable, so that several
 * test suites could be run against several servers, with different client
 * adapters and configurations.
 *
 * Note that $this->baseuri must point to a directory on a web server
 * containing all the files under the files directory. You should symlink
 * or copy these files and set 'baseuri' properly.
 *
 * You can also set the proper constand in your test configuration file to
 * point to the right place.
 *
 * @group      Laminas_Http
 * @group      Laminas_Http_Client
 */
use Laminas\Http\Client\Adapter\Socket;

class SocketPersistentTest extends SocketTest
{
    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = [
        'adapter'    => Socket::class,
        'persistent' => true,
        'keepalive'  => true,
    ];
}
