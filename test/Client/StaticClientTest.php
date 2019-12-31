<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Client;

use Laminas\Http\Client;
use Laminas\Http\ClientStatic as HTTPClient;

/**
 * This are the test for the prototype of Laminas\Http\Client
 *
 * @group      Laminas\Http
 * @group      Laminas\Http\Client
 */
class StaticClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Uri for test
     *
     * @var string
     */
    protected $baseuri;

    /**
     * Set up the test case
     */
    protected function setUp()
    {
        if (defined('TESTS_LAMINAS_HTTP_CLIENT_BASEURI')
            && (TESTS_LAMINAS_HTTP_CLIENT_BASEURI != false)) {
            $this->baseuri = TESTS_LAMINAS_HTTP_CLIENT_BASEURI;
            if (substr($this->baseuri, -1) != '/') {
                $this->baseuri .= '/';
            }
        } else {
            // Skip tests
            $this->markTestSkipped("Laminas_Http_Client dynamic tests are not enabled in TestConfiguration.php");
        }
    }

    /**
     * Test simple GET
     */
    public function testHttpSimpleGet()
    {
        $response = HTTPClient::get($this->baseuri . 'testSimpleRequests.php');
        $this->assertTrue($response->isSuccess());
    }

    /**
     * Test GET with query string in URI
     */
    public function testHttpGetWithParamsInUri()
    {
        $response = HTTPClient::get($this->baseuri . 'testGetData.php?foo');
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
    }

    /**
     * Test GET with query as params
     */
    public function testHttpMultiGetWithParam()
    {
        $response = HTTPClient::get($this->baseuri . 'testGetData.php', array('foo' => 'bar'));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
    }

    /**
     * Test GET with body
     */
    public function testHttpGetWithBody()
    {
        $getBody = 'baz';

        $response = HTTPClient::get($this->baseuri . 'testRawGetData.php',
                                   array('foo' => 'bar'),
                                   array(),
                                   $getBody);

        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
        $this->assertContains($getBody, $response->getBody());
    }

    /**
     * Test simple POST
     */
    public function testHttpSimplePost()
    {
        $response = HTTPClient::post($this->baseuri . 'testPostData.php', array('foo' => 'bar'));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
    }

    /**
     * Test POST with header Content-Type
     */
    public function testHttpPostContentType()
    {
        $response = HTTPClient::post($this->baseuri . 'testPostData.php',
                                    array('foo' => 'bar'),
                                    array('Content-Type' => Client::ENC_URLENCODED));
        $this->assertTrue($response->isSuccess());
        $this->assertContains('foo', $response->getBody());
        $this->assertContains('bar', $response->getBody());
    }

    /**
     * Test POST with body
     */
    public function testHttpPostWithBody()
    {
        $postBody = 'foo';

        $response = HTTPClient::post($this->baseuri . 'testRawPostData.php',
                                    array('foo' => 'bar'),
                                    array('Content-Type' => Client::ENC_URLENCODED),
                                    $postBody);

        $this->assertTrue($response->isSuccess());
        $this->assertContains($postBody, $response->getBody());
    }

    /**
     * Test GET with adapter configuration
     *
     * @link https://github.com/zendframework/zf2/issues/6482
     */
    public function testHttpGetUsesAdapterConfig()
    {
        $testUri = $this->baseuri . 'testSimpleRequests.php';

        $config = array(
            'useragent' => 'simplegettest'
        );

        HTTPClient::get($testUri, array(), array(), null, $config);

        $reflectedClass = new \ReflectionClass('Laminas\Http\ClientStatic');
        $property = $reflectedClass->getProperty('client');
        $property->setAccessible(true);
        $client = $property->getValue();

        $rawRequest = $client->getLastRawRequest();

        $this->assertContains('User-Agent: simplegettest', $rawRequest);
    }

    /**
     * Test POST with adapter configuration
     *
     * @link https://github.com/zendframework/zf2/issues/6482
     */
    public function testHttpPostUsesAdapterConfig()
    {
        $testUri = $this->baseuri . 'testPostData.php';

        $config = array(
            'useragent' => 'simpleposttest'
        );

        HTTPClient::post($testUri, array('foo' => 'bar'), array(), null, $config);

        $reflectedClass = new \ReflectionClass('Laminas\Http\ClientStatic');
        $property = $reflectedClass->getProperty('client');
        $property->setAccessible(true);
        $client = $property->getValue();

        $rawRequest = $client->getLastRawRequest();

        $this->assertContains('User-Agent: simpleposttest', $rawRequest);
    }
}
