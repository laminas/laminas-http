<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

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
    protected $host;
    protected $port;

    /**
     * Configuration array
     *
     * @var array
     */
    protected function setUp()
    {
        if (getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY')
            && filter_var(getenv('TESTS_LAMINAS_HTTP_CLIENT_BASEURI'), FILTER_VALIDATE_BOOLEAN)
        ) {
            list($host, $port) = explode(':', getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY'), 2);

            if (! $host) {
                $this->markTestSkipped('No valid proxy host name or address specified.');
            }

            $this->host = $host;

            $port = (int) $port;
            if ($port == 0) {
                $port = 8080;
            } else {
                if (($port < 1 || $port > 65535)) {
                    $this->markTestSkipped(sprintf(
                        '%s is not a valid proxy port number. Should be between 1 and 65535.',
                        $port
                    ));
                }
            }

            $this->port = $port;

            $user = '';
            $pass = '';
            if (getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_USER')
                && getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_USER')
            ) {
                $user = getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_USER');
            }

            if (getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_PASS')
                && getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_PASS')
            ) {
                $pass = getenv('TESTS_LAMINAS_HTTP_CLIENT_HTTP_PROXY_PASS');
            }

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
