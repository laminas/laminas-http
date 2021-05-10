<?php

namespace LaminasTest\Http\Client;

use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\Proxy;
use Laminas\Http\Client\Adapter\Socket;

/**
 * Laminas_Http_Client_Adapter_Proxy test suite.
 *
 * In order to run, TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY must point to a working
 * proxy server, which can access TESTS_LAMINAS_HTTP_CLIENT_BASEURI.
 *
 * See phpunit.xml.dist for more information.
 *
 * @group      Laminas_Http
 * @group      Laminas_Http_Client
 */
class ProxyAdapterTest extends SocketTest
{
    /** @var string */
    protected $host;

    /** @var int */
    protected $port;

    protected function setUp(): void
    {
        if (getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY')
            && filter_var(getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY'), FILTER_VALIDATE_BOOLEAN) === false
        ) {
            list($host, $port) = explode(':', getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY'), 2);

            if (! $host) {
                $this->markTestSkipped('No valid proxy host name or address specified.');
            }

            $this->host = $host;

            $port = (int) $port;
            if ($port === 0) {
                $port = 8080;
            } elseif ($port < 1 || $port > 65535) {
                $this->markTestSkipped(sprintf(
                    '%s is not a valid proxy port number. Should be between 1 and 65535.',
                    $port
                ));
            }

            $this->port = $port;

            $user = getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_USER') ?: '';
            $pass = getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_PASS') ?: '';

            $this->config = [
                'adapter'    => Proxy::class,
                'proxy_host' => $host,
                'proxy_port' => $port,
                'proxy_user' => $user,
                'proxy_pass' => $pass,
            ];

            parent::setUp();
        } else {
            $this->markTestSkipped(sprintf(
                '%s proxy server tests are not enabled in phpunit.xml',
                Client::class
            ));
        }
    }

    /**
     * Test that when no proxy is set the adapter falls back to direct connection
     */
    public function testFallbackToSocket()
    {
        $this->_adapter->setOptions([
            'proxy_host' => null,
        ]);

        $this->client->setUri($this->baseuri . 'testGetLastRequest.php');
        $res = $this->client->setMethod(\Laminas\Http\Request::METHOD_TRACE)->send();
        if ($res->getStatusCode() == 405 || $res->getStatusCode() == 501) {
            $this->markTestSkipped('Server does not allow the TRACE method');
        }

        $this->assertEquals(
            $this->client->getLastRawRequest(),
            $res->getBody(),
            'Response body should be exactly like the last request'
        );
    }

    public function testGetLastRequest()
    {
        // This test will never work for the proxy adapter (and shouldn't!)
        // because the proxy server modifies the request which is sent back in
        // the TRACE response
    }

    public function testDefaultConfig()
    {
        $config = $this->_adapter->getConfig();
        $this->assertEquals(true, $config['sslverifypeer']);
        $this->assertEquals(false, $config['sslallowselfsigned']);
    }

    /**
     * Somehow verification failed for the request through the proxy.
     * This could be an issue with Proxy/Socket adapter implementation,
     * as issue is not present from command line using curl:
     * curl -IL https://getlaminas.org -x 127.0.0.1:8081
     */
    public function testUsesProvidedArgSeparator()
    {
        $this->client->setOptions(['sslverifypeername' => false]);

        parent::testUsesProvidedArgSeparator();
    }

    /**
     * HTTP request through the proxy must be with absoluteURI
     * https://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5.1.2
     * Response contains path, not the absolute URI,
     * also Connection: close header is in the different place.
     */
    public function testGetLastRawRequest()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');
        $this->client->setParameterGet(['someinput' => 'somevalue']);
        $this->client->setHeaders([
            'X-Powered-By' => 'A lot of PHP',
        ]);

        $this->client->setMethod('TRACE');
        $res = $this->client->send();
        if ($res->getStatusCode() == 405 || $res->getStatusCode() == 501) {
            $this->markTestSkipped('Server does not allow the TRACE method');
        }

        list($schema, $host) = explode('://', $this->baseuri);
        $host = trim($host, '/');

        $this->assertSame(
            'TRACE ' . $this->baseuri . 'testHeaders.php?someinput=somevalue HTTP/1.1' . "\r\n"
            . 'Host: ' . $host . "\r\n"
            . 'Connection: close' . "\r\n"
            . 'Accept-Encoding: gzip, deflate' . "\r\n"
            . 'User-Agent: Laminas_Http_Client' . "\r\n"
            . 'X-Powered-By: A lot of PHP' . "\r\n\r\n",
            $this->client->getLastRawRequest()
        );

        $this->assertSame(
            'TRACE /testHeaders.php?someinput=somevalue HTTP/1.1' . "\r\n"
            . 'Host: ' . $host . "\r\n"
            . 'Accept-Encoding: gzip, deflate' . "\r\n"
            . 'User-Agent: Laminas_Http_Client' . "\r\n"
            . 'X-Powered-By: A lot of PHP' . "\r\n"
            . 'Connection: close' . "\r\n\r\n",
            $res->getBody()
        );
    }

    /**
     * Test that the proxy keys normalised by the client are correctly converted to what the proxy adapter expects.
     */
    public function testProxyKeysCorrectlySetInProxyAdapter()
    {
        $adapterConfig = $this->_adapter->getConfig();
        $adapterHost = $adapterConfig['proxy_host'];
        $adapterPort = $adapterConfig['proxy_port'];

        $this->assertSame($this->host, $adapterHost);
        $this->assertSame($this->port, $adapterPort);
    }

    public function testProxyHasAllSocketConfigs()
    {
        $socket = new Socket();
        $socketConfig = $socket->getConfig();
        $proxy = new Proxy();
        $proxyConfig = $proxy->getConfig();
        foreach (array_keys($socketConfig) as $socketConfigKey) {
            $this->assertArrayHasKey(
                $socketConfigKey,
                $proxyConfig,
                'Proxy adapter should have all the Socket configuration keys'
            );
        }
    }
}
