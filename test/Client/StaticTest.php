<?php

declare(strict_types=1);

namespace LaminasTest\Http\Client;

use ArrayObject;
use Laminas\Http\Client as HTTPClient;
use Laminas\Http\Client\Adapter\Exception as ClientAdapterException;
use Laminas\Http\Client\Adapter\Test;
use Laminas\Http\Client\Exception as ClientException;
use Laminas\Http\Exception\InvalidArgumentException;
use Laminas\Http\Exception\RuntimeException;
use Laminas\Http\Header\SetCookie;
use Laminas\Http\Response\Stream;
use Laminas\Uri\Http as UriHttp;
use LaminasTest\Http\Client\TestAsset\MockAdapter;
use LaminasTest\Http\Client\TestAsset\MockClient;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_shift;
use function count;
use function explode;
use function fclose;
use function file;
use function file_get_contents;
use function filesize;
use function getenv;
use function gettype;
use function is_file;
use function preg_match;
use function realpath;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function trim;
use function unlink;
use function urlencode;

use const DIRECTORY_SEPARATOR;

/**
 * This Testsuite includes all Laminas_Http_Client tests that do not rely
 * on performing actual requests to an HTTP server. These tests can be
 * executed once, and do not need to be tested with different servers /
 * client setups.
 *
 * @group      Laminas_Http
 * @group      Laminas_Http_Client
 */
class StaticTest extends TestCase
{
    /**
     * Common HTTP client
     *
     * @var HTTPClient
     */
    protected $client;

    /**
     * Set up the test suite before each test
     */
    public function setUp(): void
    {
        $this->client = new MockClient('http://www.example.com');
    }

    /**
     * Clean up after running a test
     */
    public function tearDown(): void
    {
        $this->client = null;
    }

    /**
     * URI Tests
     */

    /**
     * Test we can SET and GET a URI as string
     */
    public function testSetGetUriString()
    {
        $uristr = 'https://www.zend.com:80/';

        $this->client->setUri($uristr);

        $uri = $this->client->getUri();
        $this->assertInstanceOf(UriHttp::class, $uri, 'Returned value is not a Uri object as expected');
        $this->assertEquals($uri->__toString(), $uristr, 'Returned Uri object does not hold the expected URI');

        $uri = $this->client->getUri()->toString();
        $this->assertIsString(
            $uri,
            'Returned value expected to be a string, ' . gettype($uri) . ' returned'
        );
        $this->assertEquals($uri, $uristr, 'Returned string is not the expected URI');
    }

    /**
     * Test we can SET and GET a URI as object
     */
    public function testSetGetUriObject()
    {
        $uriobj = new UriHttp('https://www.zend.com:80/');

        $this->client->setUri($uriobj);

        $uri = $this->client->getUri();
        $this->assertInstanceOf(UriHttp::class, $uri, 'Returned value is not a Uri object as expected');
        $this->assertEquals($uri, $uriobj, 'Returned object is not the excepted Uri object');
    }

    /**
     * Test that setting the same parameter twice in the query string does not
     * get reduced to a single value only.
     */
    public function testDoubleGetParameter()
    {
        $qstr = 'foo=bar&foo=baz';

        $this->client->setUri('http://example.com/test/?' . $qstr);
        $this->client->setAdapter(Test::class);
        $this->client->setMethod('GET');
        $this->client->send();

        $this->assertStringContainsString(
            $qstr,
            $this->client->getLastRawRequest(),
            'Request is expected to contain the entire query string'
        );
    }

    /**
     * Header Tests
     */

    /**
     * Test we can get already set headers
     */
    public function testGetHeader()
    {
        $this->client->setHeaders([
            'Accept-encoding' => 'gzip,deflate',
            'Accept-language' => 'en,de,*',
        ]);

        $this->assertEquals(
            $this->client->getHeader('Accept-encoding'),
            'gzip, deflate',
            'Returned value of header is not as expected'
        );
        $this->assertEquals(
            $this->client->getHeader('X-Fake-Header'),
            null,
            'Non-existing header should not return a value'
        );
    }

    /**
     * Authentication tests
     */

    /**
     * Test setAuth (dynamic method) fails when trying to use an unsupported
     * authentication scheme
     */
    public function testExceptUnsupportedAuthDynamic()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or not supported authentication type: \'SuperStrongAlgo\'');

        $this->client->setAuth('shahar', '1234', 'SuperStrongAlgo');
    }

    /**
     * Cookie and Cookie Jar tests
     */

    /**
     * Test we can properly set a new cookies
     */
    public function testSetNewCookies()
    {
        $this->client->addCookie('cookie', 'value');
        $this->client->addCookie('chocolate', 'chips');
        $cookies = $this->client->getCookies();

        // Check we got the right cookiejar
        $this->assertIsArray($cookies);
        $this->assertContainsOnlyInstancesOf(SetCookie::class, $cookies);
        $this->assertCount(2, $cookies);
    }

    /**
     * Test we can unset a cookie jar
     */
    public function testUnsetCookies()
    {
        // Set the cookie jar just like in testSetNewCookieJar
        $this->client->addCookie('cookie', 'value');
        $this->client->addCookie('chocolate', 'chips');
        $cookies = $this->client->getCookies();

        // Try unsetting the cookies
        $this->client->clearCookies();
        $cookies = $this->client->getCookies();

        $this->assertEquals([], $cookies, 'Cookies are expected to be an empty array but it is not');
    }

    /**
     * Make sure using an invalid cookie jar object throws an exception
     */
    public function testSetInvalidCookies()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid parameter type passed as Cookie');

        $this->client->addCookie('cookie');
    }

    /**
     * Configuration Handling
     */

    /**
     * Test that we can set a valid configuration array with some options
     */
    public function testConfigSetAsArray()
    {
        $config = [
            'timeout'    => 500,
            'someoption' => 'hasvalue',
        ];

        $this->client->setOptions($config);

        $hasConfig = $this->client->config;

        foreach ($config as $k => $v) {
            $this->assertEquals($v, $hasConfig[$k]);
        }
    }

    /**
     * Test that a Traversable object can be used to set configuration
     *
     * @link https://framework.zend.com/issues/browse/ZEND-5577
     */
    public function testConfigSetAsTraversable()
    {
        $config = new ArrayObject([
            'timeout' => 400,
            'nested'  => [
                'item' => 'value',
            ],
        ]);

        $this->client->setOptions($config);

        $hasConfig = $this->client->config;
        $this->assertEquals($config['timeout'], $hasConfig['timeout']);
        $this->assertEquals($config['nested']['item'], $hasConfig['nested']['item']);
    }

    /**
     * Test that passing invalid variables to setConfig() causes an exception
     *
     * @dataProvider invalidConfigProvider
     * @param mixed $config
     */
    public function testConfigSetInvalid($config)
    {
        $this->expectException(ClientException\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config parameter is not valid');

        $this->client->setOptions($config);
    }

    /**
     * Test that configuration options are passed to the adapter after the
     * adapter is instantiated
     *
     * @group Laminas-4557
     */
    public function testConfigPassToAdapterLaminas4557()
    {
        $adapter = new MockAdapter();

        // test that config passes when we set the adapter
        $this->client->setOptions(['param' => 'value1']);
        $this->client->setAdapter($adapter);
        $adapterCfg = $adapter->config;
        $this->assertEquals('value1', $adapterCfg['param']);

        // test that adapter config value changes when we set client config
        $this->client->setOptions(['param' => 'value2']);
        $adapterCfg = $adapter->config;
        $this->assertEquals('value2', $adapterCfg['param']);
    }

    /**
     * Other Tests
     */

    /**
     * Test the getLastRawResponse() method actually returns the last response
     */
    public function testGetLastRawResponse()
    {
        // First, make sure we get null before the request
        $this->assertEquals(
            null,
            $this->client->getLastRawResponse(),
            'getLastRawResponse() is still expected to return null'
        );

        // Now, test we get a proper response after the request
        $this->client->setUri('http://example.com/foo/bar');
        $this->client->setAdapter(Test::class);

        $response = $this->client->send();
        $this->assertSame(
            $response,
            $this->client->getResponse(),
            'Response is expected to be identical to the result of getResponse()'
        );
    }

    /**
     * Test that getLastRawResponse returns null when not storing
     */
    public function testGetLastRawResponseWhenNotStoring()
    {
        // Now, test we get a proper response after the request
        $this->client->setUri('http://example.com/foo/bar');
        $this->client->setAdapter(Test::class);
        $this->client->setOptions(['storeresponse' => false]);

        $this->client->send();

        $this->assertNull(
            $this->client->getLastRawResponse(),
            'getLastRawResponse is expected to be null when not storing'
        );
    }

    /**
     * Check we get an exception when trying to send a POST request with an
     * invalid content-type header
     */
    public function testInvalidPostContentType()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot handle content type \'x-foo/something-fake\' automatically');

        $this->client->setEncType('x-foo/something-fake');
        $this->client->setParameterPost(['parameter' => 'value']);
        $this->client->setMethod('POST');
        // This should throw an exception
        $this->client->send();
    }

    /**
     * Check we get an exception if there's an error in the socket
     */
    public function testSocketErrorException()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $this->expectException(ClientAdapterException\RuntimeException::class);
        $this->expectExceptionMessage('Unable to connect to 255.255.255.255:80');

        // Try to connect to an invalid host
        $this->client->setUri('http://255.255.255.255');

        // Reduce timeout to 3 seconds to avoid waiting
        $this->client->setOptions(['timeout' => 3]);

        // This call should cause an exception
        $this->client->send();
    }

    /**
     * Check that an exception is thrown if non-word characters are used in
     * the request method.
     *
     * @dataProvider invalidMethodProvider
     * @param string $method
     */
    public function testSettingInvalidMethodThrowsException($method)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method passed');

        $this->client->setMethod($method);
    }

    /**
     * Test that POST data with multi-dimensional array is properly encoded as
     * multipart/form-data
     */
    public function testFormDataEncodingWithMultiArrayLaminas7038()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $this->client->setAdapter(Test::class);
        $this->client->setUri('http://example.com');
        $this->client->setEncType(HTTPClient::ENC_FORMDATA);

        $this->client->setParameterPost([
            'test' => [
                'v0.1',
                'v0.2',
                'k1' => 'v1.0',
                'k2' => [
                    'v2.1',
                    'k2.1' => 'v2.1.0',
                ],
            ],
        ]);

        $this->client->setMethod('POST');
        $this->client->send();

        $expectedLines = file(__DIR__ . '/_files/Laminas7038-multipartarrayrequest.txt');

        $gotLines = explode("\n", $this->client->getLastRawRequest());

        $this->assertEquals(count($expectedLines), count($gotLines));

        while (
            ($expected = array_shift($expectedLines))
            && ($got = array_shift($gotLines))
        ) {
            $expected = trim($expected);
            $got      = trim($got);
            $this->assertMatchesRegularExpression(sprintf('/^%s$/', $expected), $got);
        }
    }

    /**
     * Test that we properly calculate the content-length of multibyte-encoded
     * request body
     *
     * This may file in case that mbstring overloads the substr and strlen
     * functions, and the mbstring internal encoding is a multibyte encoding.
     *
     * @link https://getlaminas.org/issues/browse/Laminas-2098
     */
    public function testMultibyteRawPostDataLaminas2098()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $this->client->setAdapter(Test::class);
        $this->client->setUri('http://example.com');

        $bodyFile = __DIR__ . '/_files/Laminas2098-multibytepostdata.txt';

        $this->client->setRawBody(file_get_contents($bodyFile));
        $this->client->setEncType('text/plain');
        $this->client->setMethod('POST');
        $this->client->send();
        $request = $this->client->getLastRawRequest();

        if (! preg_match('/^content-length:\s+(\d+)/mi', $request, $match)) {
            $this->fail('Unable to find content-length header in request');
        }

        $this->assertEquals(filesize($bodyFile), (int) $match[1]);
    }

    /**
     * Testing if the connection isn't closed
     *
     * @group Laminas-9685
     */
    public function testOpenTempStreamWithValidFileDoesntThrowsException()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $url    = 'http://www.example.com/';
        $config = [
            'outputstream' => realpath(__DIR__ . '/_files/laminas_http_client_stream.file'),
        ];
        $client = new HTTPClient($url, $config);

        $result = $client->send();

        // we can safely return until we can verify link is still active
        // @todo verify link is still active
    }

    /**
     * Test if a downloaded file can be deleted
     *
     * @group Laminas-9685
     */
    public function testDownloadedFileCanBeDeleted()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped('Laminas\Http\Client online tests are not enabled');
        }
        $url        = 'http://www.example.com/';
        $outputFile = @tempnam(@sys_get_temp_dir(), 'zht');
        if (! is_file($outputFile)) {
            $this->markTestSkipped('Failed to create a temporary file');
        }
        $config = [
            'outputstream' => $outputFile,
        ];
        $client = new HTTPClient($url, $config);

        $result = $client->send();

        $this->assertInstanceOf(Stream::class, $result);
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->assertFalse(@unlink($outputFile), 'Deleting an open file should fail on Windows');
        }
        fclose($result->getStream());
        $this->assertTrue(@unlink($outputFile), 'Failed to delete downloaded file');
    }

    /**
     * Testing if the connection can be closed
     *
     * @group Laminas-9685
     */
    public function testOpenTempStreamWithBogusFileClosesTheConnection()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }

        $url    = 'http://www.example.com';
        $config = [
            'outputstream' => '/path/to/bogus/file.ext',
        ];
        $client = new HTTPClient($url, $config);
        $client->setMethod('GET');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not open temp file /path/to/bogus/file.ext');
        $client->send();
    }

    /**
     * Test sending cookie with encoded value
     *
     * @group fix-double-encoding-problem-about-cookie-value
     */
    public function testEncodedCookiesInRequestHeaders()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $this->client->addCookie('foo', 'bar=baz');
        $this->client->send();
        $cookieValue = 'Cookie: foo=' . urlencode('bar=baz');
        $this->assertStringContainsString(
            $cookieValue,
            $this->client->getLastRawRequest(),
            'Request is expected to contain the entire cookie "keyname=encoded_value"'
        );
    }

    /**
     * Test sending cookie header with raw value
     *
     * @group fix-double-encoding-problem-about-cookie-value
     */
    public function testRawCookiesInRequestHeaders()
    {
        if (! getenv('TESTS_LAMINAS_HTTP_CLIENT_ONLINE')) {
            $this->markTestSkipped(sprintf(
                '%s online tests are not enabled',
                HTTPClient::class
            ));
        }
        $this->client->setOptions(['encodecookies' => false]);
        $this->client->addCookie('foo', 'bar=baz');
        $this->client->send();
        $cookieValue = 'Cookie: foo=bar=baz';
        $this->assertStringContainsString(
            $cookieValue,
            $this->client->getLastRawRequest(),
            'Request is expected to contain the entire cookie "keyname=raw_value"'
        );
    }

    /**
     * Data providers
     */

    /**
     * Data provider of valid non-standard HTTP methods
     *
     * @return array
     */
    public static function validMethodProvider()
    {
        return [
            ['OPTIONS'],
            ['POST'],
            ['DOSOMETHING'],
            ['PROPFIND'],
            ['Some_Characters'],
            ['X-MS-ENUMATTS'],
        ];
    }

    /**
     * Data provider of invalid HTTP methods
     *
     * @return array
     */
    public static function invalidMethodProvider()
    {
        return [
            ['N@5TYM3T#0D'],
            ['TWO WORDS'],
            ['GET http://foo.com/?'],
            ['Injected' . "\n" . 'newline'],
        ];
    }

    /**
     * Data provider for invalid configuration containers
     *
     * @return array
     */
    public static function invalidConfigProvider()
    {
        return [
            [false],
            ['foobar'],
            ['foo' => 'bar'],
            [null],
            [new stdClass()],
            [55],
        ];
    }
}
