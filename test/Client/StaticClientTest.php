<?php

namespace LaminasTest\Http\Client;

use Laminas\Http\Client;
use Laminas\Http\ClientStatic as HTTPClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * This are the test for the prototype of Laminas\Http\Client
 *
 * @group      Laminas\Http
 * @group      Laminas\Http\Client
 */
class StaticClientTest extends TestCase
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
    protected function setUp(): void
    {
        if (getenv('TESTS_LAMINAS_HTTP_CLIENT_BASEURI')
            && (filter_var(getenv('TESTS_LAMINAS_HTTP_CLIENT_BASEURI'), FILTER_VALIDATE_BOOLEAN) != false)) {
            $this->baseuri = getenv('TESTS_LAMINAS_HTTP_CLIENT_BASEURI');
            if (substr($this->baseuri, -1) != '/') {
                $this->baseuri .= '/';
            }
        } else {
            // Skip tests
            $this->markTestSkipped(sprintf(
                '%s dynamic tests are not enabled in phpunit.xml',
                HTTPClient::class
            ));
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
        $this->assertStringContainsString('foo', $response->getBody());
    }

    /**
     * Test GET with query as params
     */
    public function testHttpMultiGetWithParam()
    {
        $response = HTTPClient::get($this->baseuri . 'testGetData.php', ['foo' => 'bar']);
        $this->assertTrue($response->isSuccess());
        $this->assertStringContainsString('foo', $response->getBody());
        $this->assertStringContainsString('bar', $response->getBody());
    }

    /**
     * Test GET with body
     */
    public function testHttpGetWithBody()
    {
        $getBody = 'baz';

        $response = HTTPClient::get(
            $this->baseuri . 'testRawGetData.php',
            ['foo' => 'bar'],
            [],
            $getBody
        );

        $this->assertTrue($response->isSuccess());
        $this->assertStringContainsString('foo', $response->getBody());
        $this->assertStringContainsString('bar', $response->getBody());
        $this->assertStringContainsString($getBody, $response->getBody());
    }

    /**
     * Test simple POST
     */
    public function testHttpSimplePost()
    {
        $response = HTTPClient::post($this->baseuri . 'testPostData.php', ['foo' => 'bar']);
        $this->assertTrue($response->isSuccess());
        $this->assertStringContainsString('foo', $response->getBody());
        $this->assertStringContainsString('bar', $response->getBody());
    }

    /**
     * Test POST with header Content-Type
     */
    public function testHttpPostContentType()
    {
        $response = HTTPClient::post(
            $this->baseuri . 'testPostData.php',
            ['foo' => 'bar'],
            ['Content-Type' => Client::ENC_URLENCODED]
        );
        $this->assertTrue($response->isSuccess());
        $this->assertStringContainsString('foo', $response->getBody());
        $this->assertStringContainsString('bar', $response->getBody());
    }

    /**
     * Test POST with body
     */
    public function testHttpPostWithBody()
    {
        $postBody = 'foo';

        $response = HTTPClient::post(
            $this->baseuri . 'testRawPostData.php',
            ['foo' => 'bar'],
            ['Content-Type' => Client::ENC_URLENCODED],
            $postBody
        );

        $this->assertTrue($response->isSuccess());
        $this->assertStringContainsString($postBody, $response->getBody());
    }

    /**
     * Test GET with adapter configuration
     *
     * @link https://github.com/zendframework/zf2/issues/6482
     */
    public function testHttpGetUsesAdapterConfig()
    {
        $testUri = $this->baseuri . 'testSimpleRequests.php';

        $config = [
            'useragent' => 'simplegettest',
        ];

        HTTPClient::get($testUri, [], [], null, $config);

        $reflectedClass = new ReflectionClass(HTTPClient::class);
        $property = $reflectedClass->getProperty('client');
        $property->setAccessible(true);
        $client = $property->getValue();

        $rawRequest = $client->getLastRawRequest();

        $this->assertStringContainsString('User-Agent: simplegettest', $rawRequest);
    }

    /**
     * Test POST with adapter configuration
     *
     * @link https://github.com/zendframework/zf2/issues/6482
     */
    public function testHttpPostUsesAdapterConfig()
    {
        $testUri = $this->baseuri . 'testPostData.php';

        $config = [
            'useragent' => 'simpleposttest',
        ];

        HTTPClient::post($testUri, ['foo' => 'bar'], [], null, $config);

        $reflectedClass = new ReflectionClass(HTTPClient::class);
        $property = $reflectedClass->getProperty('client');
        $property->setAccessible(true);
        $client = $property->getValue();

        $rawRequest = $client->getLastRawRequest();

        $this->assertStringContainsString('User-Agent: simpleposttest', $rawRequest);
    }
}
