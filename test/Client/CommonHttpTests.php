<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.AbstractClass.Prefix

namespace LaminasTest\Http\Client;

use Exception;
use Laminas\Http\Client as HTTPClient;
use Laminas\Http\Client\Adapter;
use Laminas\Http\Client\Adapter\AdapterInterface;
use Laminas\Http\Client\Adapter\Exception as AdapterException;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Http\Request;
use Laminas\Http\Response\Stream;
use Laminas\Stdlib\Parameters;
use PHPUnit\Framework\TestCase;
use stdClass;

use function array_merge;
use function basename;
use function ceil;
use function dirname;
use function file_get_contents;
use function filesize;
use function filter_var;
use function finfo_open;
use function fopen;
use function function_exists;
use function getenv;
use function implode;
use function ini_get;
use function is_array;
use function is_string;
use function microtime;
use function mime_content_type;
use function rtrim;
use function serialize;
use function sprintf;
use function str_replace;
use function stream_get_contents;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use function sys_get_temp_dir;
use function tempnam;

use const FILEINFO_MIME;
use const FILTER_VALIDATE_URL;

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
 * You can also set the proper constant in your test configuration file to
 * point to the right place.
 *
 * @group      Laminas_Http
 * @group      Laminas_Http_Client
 */
abstract class CommonHttpTests extends TestCase
{
    /**
     * The bast URI for this test, containing all files in the files directory
     * Should be set in phpunit.xml or phpunit.xml.dist
     *
     * @var string
     */
    protected $baseuri;

    /**
     * Common HTTP client
     *
     * @var HTTPClient
     */
    protected $client;

    /**
     * Common HTTP client adapter
     *
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = [
        'adapter' => Socket::class,
    ];

    /**
     * Set up the test case
     */
    protected function setUp(): void
    {
        $baseUri = getenv('TESTS_LAMINAS_HTTP_CLIENT_BASEURI');

        if ($baseUri && filter_var($baseUri, FILTER_VALIDATE_URL) !== false) {
            $this->baseuri = $baseUri;
            if (substr($this->baseuri, -1) !== '/') {
                $this->baseuri .= '/';
            }

            $name = $this->getName();
            if (($pos = strpos($name, ' ')) !== false) {
                $name = substr($name, 0, $pos);
            }

            $uri = $this->baseuri . $name . '.php';

            $this->adapter = new $this->config['adapter']();
            $this->client  = new HTTPClient($uri, $this->config);
            $this->client->setAdapter($this->adapter);
        } else {
            // Skip tests
            $this->markTestSkipped(sprintf(
                '%s dynamic tests are not enabled in phpunit.xml',
                HTTPClient::class
            ));
        }
    }

    /**
     * Clean up the test environment
     */
    protected function tearDown(): void
    {
        $this->client  = null;
        $this->adapter = null;
    }

    // Simple request tests

    /** @psalm-return array<array-key, array{0: Request::METHOD_*}> */
    public function methodProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_POST],
            [Request::METHOD_OPTIONS],
            [Request::METHOD_PUT],
            [Request::METHOD_DELETE],
            [Request::METHOD_PATCH],
        ];
    }

    /**
     * Test simple requests
     *
     * @dataProvider methodProvider
     * @param string $method
     */
    public function testSimpleRequests($method)
    {
        $this->client->setMethod($method);
        $res = $this->client->send();
        $this->assertTrue($res->isSuccess(), sprintf('HTTP %s request failed.', $method));
    }

    /**
     * Test we can get the last request as string
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
        if ($res->getStatusCode() === 405 || $res->getStatusCode() === 501) {
            $this->markTestSkipped('Server does not allow the TRACE method');
        }

        $this->assertEquals(
            $this->client->getLastRawRequest(),
            $res->getBody(),
            'Response body should be exactly like the last request'
        );
    }

    /**
     * GET and POST parameters tests
     */

    /**
     * Test we can properly send GET parameters
     *
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testGetData(array $params)
    {
        $this->client->setUri($this->client->getUri() . '?name=Arthur');
        $this->client->setParameterGet($params);
        $res = $this->client->send();
        $this->assertEquals(serialize(array_merge(['name' => 'Arthur'], $params)), $res->getBody());
    }

    /**
     * Test we can properly send POST parameters with
     * application/x-www-form-urlencoded content type
     *
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testPostDataUrlEncoded(array $params)
    {
        $this->client->setUri($this->baseuri . 'testPostData.php');
        $this->client->setEncType(HTTPClient::ENC_URLENCODED);

        $this->client->setParameterPost($params);

        $this->client->setMethod('POST');
        $this->assertFalse($this->client->getRequest()->isPatch());
        $res = $this->client->send();
        $this->assertEquals(serialize($params), $res->getBody(), 'POST data integrity test failed');
    }

    /**
     * Test we can properly send PATCH parameters with
     * application/x-www-form-urlencoded content type
     *
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testPatchData(array $params)
    {
        $client = $this->client;
        $client->setUri($this->baseuri . 'testPatchData.php');

        $client->setRawBody(serialize($params));

        $client->setMethod('PATCH');
        $this->assertEquals($client::ENC_URLENCODED, $this->client->getEncType());
        $this->assertTrue($client->getRequest()->isPatch());
        $res = $this->client->send();
        $this->assertEquals(serialize($params), $res->getBody(), 'PATCH data integrity test failed');
    }

    /**
     * Test we can properly send DELETE parameters with
     * application/x-www-form-urlencoded content type
     *
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testDeleteData(array $params)
    {
        $client = $this->client;
        $client->setUri($this->baseuri . 'testDeleteData.php');

        $client->setRawBody(serialize($params));

        $client->setMethod('DELETE');
        $this->assertEquals($client::ENC_URLENCODED, $this->client->getEncType());
        $this->assertTrue($client->getRequest()->isDelete());
        $res = $this->client->send();
        $this->assertEquals(serialize($params), $res->getBody(), 'DELETE data integrity test failed');
    }

    /**
     * Test we can properly send OPTIONS parameters with
     * application/x-www-form-urlencoded content type
     *
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testOptionsData(array $params)
    {
        $client = $this->client;
        $client->setUri($this->baseuri . 'testOptionsData.php');

        $client->setRawBody(serialize($params));

        $client->setMethod('OPTIONS');
        $this->assertEquals($client::ENC_URLENCODED, $this->client->getEncType());
        $this->assertTrue($client->getRequest()->isOptions());
        $res = $this->client->send();
        $this->assertEquals(serialize($params), $res->getBody(), 'OPTIONS data integrity test failed');
    }

    /**
     * Test we can properly send POST parameters with
     * multipart/form-data content type
     *
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testPostDataMultipart(array $params)
    {
        $this->client->setUri($this->baseuri . 'testPostData.php');
        $this->client->setEncType(HTTPClient::ENC_FORMDATA);
        $this->client->setParameterPost($params);
        $this->client->setMethod('POST');
        $res = $this->client->send();
        $this->assertEquals(serialize($params), $res->getBody(), 'POST data integrity test failed');
    }

    /**
     * Test using raw HTTP POST data
     */
    public function testRawPostData()
    {
        $data = 'Chuck Norris never wet his bed as a child. The bed wet itself out of fear.';

        $this->client->setRawBody($data);
        $this->client->setEncType('text/html');
        $this->client->setMethod('POST');
        $res = $this->client->send();
        $this->assertEquals($data, $res->getBody(), 'Response body does not contain the expected data');
    }

    /**
     * Make sure we can reset the parameters between consecutive requests
     */
    public function testResetParameters()
    {
        $params = [
            'quest'        => 'To seek the holy grail',
            'YourMother'   => 'Was a hamster',
            'specialChars' => '<>$+ &?=[]^%',
            'array'        => ['firstItem', 'secondItem', '3rdItem'],
        ];

        $headers = ['X-Foo' => 'bar'];

        $this->client->setParameterPost($params);
        $this->client->setParameterGet($params);
        $this->client->setHeaders($headers);
        $this->client->setMethod('POST');

        $res = $this->client->send();

        $this->assertStringContainsString(
            serialize($params) . "\n" . serialize($params),
            $res->getBody(),
            'returned body does not contain all GET and POST parameters (it should!)'
        );

        $this->client->resetParameters();
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $this->assertStringNotContainsString(
            serialize($params),
            $res->getBody(),
            "returned body contains GET or POST parameters (it shouldn't!)"
        );
        // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
        $headerXFoo = $this->client->getHeader('X-Foo');
        $this->assertEmpty($headerXFoo, 'Header not preserved by reset');
        // phpcs:enable
    }

    /**
     * Test parameters get reset when we unset them
     */
    public function testParameterUnset()
    {
        $this->client->setUri($this->baseuri . 'testResetParameters.php');

        $gparams = [
            'cheese' => 'camambert',
            'beer'   => 'jever pilnsen',
        ];

        $pparams = [
            'from' => 'bob',
            'to'   => 'alice',
        ];

        $this->client->setParameterGet($gparams)->setParameterPost($pparams);

        // Remove some parameters
        $this->client->setParameterGet(['cheese' => null])
                     ->setParameterPost(['to' => null]);
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $this->assertStringNotContainsString(
            'cheese',
            $res->getBody(),
            'The "cheese" GET parameter was expected to be unset'
        );
        $this->assertStringNotContainsString(
            'alice',
            $res->getBody(),
            'The "to" POST parameter was expected to be unset'
        );
    }

    /**
     * Header Tests
     */

    /**
     * Make sure we can set a single header
     */
    public function testHeadersSingle()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');

        $headers = [
            'Accept-encoding' => 'gzip, deflate',
            'X-baz'           => 'Foo',
            'X-powered-by'    => 'A large wooden badger',
            'Accept'          => 'text/xml, text/html, */*',
        ];

        $this->client->setHeaders($headers);
        $this->client->setMethod('TRACE');

        $res = $this->client->send();
        if ($res->getStatusCode() === 405 || $res->getStatusCode() === 501) {
            $this->markTestSkipped('Server does not allow the TRACE method');
        }

        $body = strtolower($res->getBody());

        foreach ($headers as $key => $val) {
            $this->assertStringContainsString(strtolower($key . ': ' . $val), $body);
        }
    }

    /**
     * Test we can set an array of headers
     */
    public function testHeadersArray()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');

        $headers = [
            'Accept-encoding' => 'gzip, deflate',
            'X-baz'           => 'Foo',
            'X-powered-by'    => 'A large wooden badger',
            'Accept: text/xml, text/html, */*',
        ];

        $this->client->setHeaders($headers);
        $this->client->setMethod('TRACE');

        $res = $this->client->send();
        if ($res->getStatusCode() === 405 || $res->getStatusCode() === 501) {
            $this->markTestSkipped('Server does not allow the TRACE method');
        }

        $body = strtolower($res->getBody());

        foreach ($headers as $key => $val) {
            if (is_string($key)) {
                $this->assertStringContainsString(strtolower($key . ': ' . $val), $body);
            } else {
                $this->assertStringContainsString(strtolower($val), $body);
            }
        }
    }

    /**
     * Test we can set a set of values for one header
     */
    public function testMultipleHeader()
    {
        $this->client->setUri($this->baseuri . 'testHeaders.php');
        $headers = [
            'Accept-encoding' => 'gzip, deflate',
            'X-baz'           => 'Foo',
            'X-powered-by'    => [
                'A large wooden badger',
                'My Shiny Metal Ass',
                'Dark Matter',
            ],
            'Cookie'          => [
                'foo=bar',
                'baz=waka',
            ],
        ];

        $this->client->setHeaders($headers);
        $this->client->setMethod('TRACE');

        $res = $this->client->send();
        if ($res->getStatusCode() === 405 || $res->getStatusCode() === 501) {
            $this->markTestSkipped('Server does not allow the TRACE method');
        }
        $body = strtolower($res->getBody());

        foreach ($headers as $key => $val) {
            if (is_array($val)) {
                $val = implode('; ', $val);
            }

            $this->assertStringContainsString(strtolower($key . ': ' . $val), $body);
        }
    }

    /**
     * Redirection tests
     */

    /**
     * Test the client properly redirects in default mode
     */
    public function testRedirectDefault()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet(['swallow' => 'african']);
        $this->client->setParameterPost(['Camelot' => 'A silly place']);

        // Request
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $this->assertEquals(3, $this->client->getRedirectionsCount(), 'Redirection counter is not as expected');

        // Make sure the body does *not* contain the set parameters
        $this->assertStringNotContainsString('swallow', $res->getBody());
        $this->assertStringNotContainsString('Camelot', $res->getBody());
    }

    /**
     * @link  https://getlaminas.org/issues/browse/Laminas-122
     *
     * @group Laminas-4136
     */
    public function testRedirectPersistsCookies()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet(['swallow' => 'african']);
        $this->client->setParameterPost(['Camelot' => 'A silly place']);

        // Send POST request
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $this->assertEquals(3, $this->client->getRedirectionsCount(), 'Redirection counter is not as expected');

        // Make sure the body does *not* contain the set parameters
        $this->assertStringNotContainsString('swallow', $res->getBody());
        $this->assertStringNotContainsString('Camelot', $res->getBody());

        // Check that we have received and persisted expected cookies
        $cookies = $this->client->getCookies();
        $this->assertIsArray($cookies, 'Client is not sending cookies on redirect');
        $this->assertArrayHasKey('laminastestSessionCookie', $cookies, 'Client is not sending cookies on redirect');
        $this->assertArrayHasKey('laminastestLongLivedCookie', $cookies, 'Client is not sending cookies on redirect');
        $this->assertEquals('positive', $cookies['laminastestSessionCookie']->getValue());
        $this->assertEquals('positive', $cookies['laminastestLongLivedCookie']->getValue());

        // Check that expired cookies are not passed on
        $this->assertArrayNotHasKey('laminastestExpiredCookie', $cookies, 'Expired cookies are not removed.');
    }

    /**
     * Make sure the client properly redirects in strict mode
     */
    public function testRedirectStrict()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet(['swallow' => 'african']);
        $this->client->setParameterPost(['Camelot' => 'A silly place']);

        // Set strict redirections
        $this->client->setOptions(['strictredirects' => true]);

        // Request
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $this->assertEquals(3, $this->client->getRedirectionsCount(), 'Redirection counter is not as expected');

        // Make sure the body *does* contain the set parameters
        $this->assertStringContainsString('swallow', $res->getBody());
        $this->assertStringContainsString('Camelot', $res->getBody());
    }

    /**
     * Make sure redirections stop when limit is exceeded
     */
    public function testMaxRedirectsExceeded()
    {
        $this->client->setUri($this->baseuri . 'testRedirections.php');

        // Set some parameters
        $this->client->setParameterGet(['swallow' => 'african']);
        $this->client->setParameterPost(['Camelot' => 'A silly place']);

        // Set lower max redirections
        // Try with strict redirections first
        $this->client->setOptions(['strictredirects' => true, 'maxredirects' => 2]);

        $this->client->setMethod('POST');
        $res = $this->client->send();
        $this->assertTrue(
            $res->isRedirect(),
            sprintf(
                'Last response was not a redirection as expected. Response code: %d. '
                . 'Redirections counter: %d (when strict redirects are on)',
                $res->getStatusCode(),
                $this->client->getRedirectionsCount()
            )
        );

        // Then try with normal redirections
        $this->client->setParameterGet(['redirection' => '0']);
        $this->client->setOptions(['strictredirects' => false]);
        $this->client->setMethod('POST');
        $res = $this->client->send();
        $this->assertTrue(
            $res->isRedirect(),
            sprintf(
                'Last response was not a redirection as expected. Response code: %d.'
                . ' Redirections counter: %d (when strict redirects are off)',
                $res->getStatusCode(),
                $this->client->getRedirectionsCount()
            )
        );
    }

    /**
     * Test we can properly redirect to an absolute path (not full URI)
     */
    public function testAbsolutePathRedirect()
    {
        $this->client->setUri($this->baseuri . 'testRelativeRedirections.php');
        $this->client->setParameterGet(['redirect' => 'abpath']);
        $this->client->setOptions(['maxredirects' => 1]);

        // Get the host and port part of our baseuri
        $port = $this->client->getUri()->getPort() === 80 ? '' : ':' . $this->client->getUri()->getPort();
        $uri  = $this->client->getUri()->getScheme() . '://' . $this->client->getUri()->getHost() . $port;

        $res = $this->client->send();

        $this->assertEquals(
            sprintf('%s/path/to/fake/file.ext?redirect=abpath', $uri),
            $this->client->getUri()->toString(),
            sprintf('The new location is not as expected: %s', $this->client->getUri()->toString())
        );
    }

    /**
     * Test we can properly redirect to a relative path
     */
    public function testRelativePathRedirect()
    {
        $this->client->setUri($this->baseuri . 'testRelativeRedirections.php');
        $this->client->setParameterGet(['redirect' => 'relpath']);
        $this->client->setOptions(['maxredirects' => 1]);

        // Set the new expected URI
        $uri = clone $this->client->getUri();
        $uri->setPath(rtrim(dirname($uri->getPath()), '/') . '/path/to/fake/file.ext');
        $uri = $uri->__toString();

        $this->client->send();

        $this->assertEquals(
            sprintf('%s?redirect=relpath', $uri),
            $this->client->getUri()->toString(),
            sprintf('The new location is not as expected: %s', $this->client->getUri()->toString())
        );
    }

    /**
     * HTTP Authentication Tests
     */

    /**
     * Test we can properly use Basic HTTP authentication
     */
    public function testHttpAuthBasic()
    {
        $this->client->setUri($this->baseuri . 'testHttpAuth.php');
        $this->client->setParameterGet([
            'user'   => 'alice',
            'pass'   => 'secret',
            'method' => 'Basic',
        ]);

        // First - fail password
        $this->client->setAuth('alice', 'wrong');
        $res = $this->client->send();
        $this->assertEquals(401, $res->getStatusCode(), 'Expected HTTP 401 response was not received');

        // Now use good password
        $this->client->setAuth('alice', 'secret');
        $res = $this->client->send();
        $this->assertEquals(200, $res->getStatusCode(), 'Expected HTTP 200 response was not received');
    }

    /**
     * Test that we can properly use Basic HTTP authentication by specifying username and password
     * in the URI
     */
    public function testHttpAuthBasicWithCredentialsInUri()
    {
        $uri = str_replace('http://', 'http://%s:%s@', $this->baseuri) . 'testHttpAuth.php';

        $this->client->setParameterGet([
            'user'   => 'alice',
            'pass'   => 'secret',
            'method' => 'Basic',
        ]);

        // First - fail password
        $this->client->setUri(sprintf($uri, 'alice', 'wrong'));
        $this->client->setMethod('GET');
        $res = $this->client->send();
        $this->assertEquals(401, $res->getStatusCode(), 'Expected HTTP 401 response was not received');

        // Now use good password
        $this->client->setUri(sprintf($uri, 'alice', 'secret'));
        $this->client->setMethod('GET');
        $res = $this->client->send();
        $this->assertEquals(200, $res->getStatusCode(), 'Expected HTTP 200 response was not received');
    }

    /**
     * Cookie and Cookies Tests
     */

    /**
     * Test we can set string cookies with no jar
     */
    public function testCookiesStringNoJar()
    {
        $this->client->setUri($this->baseuri . 'testCookies.php');

        $cookies = [
            'name'   => 'value',
            'cookie' => 'crumble',
        ];

        $this->client->setCookies($cookies);

        $res = $this->client->send();

        $this->assertEquals(
            $res->getBody(),
            serialize($cookies),
            'Response body does not contain the expected cookies'
        );
    }

    /**
     * Make sure we can set an array of object cookies
     */
    public function testSetCookieObjectArray()
    {
        $this->client->setUri($this->baseuri . 'testCookies.php');
        $refuri = $this->client->getUri();

        $cookies = [
            'chocolate' => 'chips',
            'crumble'   => 'apple',
            'another'   => 'cookie',
        ];

        $this->client->setCookies($cookies);

        $res = $this->client->send();
        $this->assertEquals(
            $res->getBody(),
            serialize($cookies),
            'Response body does not contain the expected cookies'
        );
    }

    /**
     * Make sure we can set an array of string cookies
     */
    public function testSetCookieStringArray()
    {
        $this->client->setUri($this->baseuri . 'testCookies.php');

        $cookies = [
            'chocolate' => 'chips',
            'crumble'   => 'apple',
            'another'   => 'cookie',
        ];

        $this->client->setCookies($cookies);

        $res = $this->client->send();
        $this->assertEquals(
            $res->getBody(),
            serialize($cookies),
            'Response body does not contain the expected cookies'
        );
    }

    /**
     * File Upload Tests
     */

    /**
     * Test we can upload raw data as a file
     */
    public function testUploadRawData()
    {
        if (! ini_get('file_uploads')) {
            $this->markTestSkipped('File uploads disabled.');
        }

        $this->client->setUri($this->baseuri . 'testUploads.php');

        $rawdata = file_get_contents(__FILE__);
        $this->client->setFileUpload('myfile.txt', 'uploadfile', $rawdata, 'text/plain');
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $body = sprintf('uploadfile myfile.txt text/plain %d' . "\n", strlen($rawdata));
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters');
    }

    /**
     * Test we can upload an existing file
     */
    public function testUploadLocalFile()
    {
        if (! ini_get('file_uploads')) {
            $this->markTestSkipped('File uploads disabled.');
        }

        $this->client->setUri($this->baseuri . 'testUploads.php');
        $this->client->setFileUpload(__FILE__, 'uploadfile', null, 'text/x-foo-bar');
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $size = filesize(__FILE__);

        $body = sprintf('uploadfile %s text/x-foo-bar %d' . "\n", basename(__FILE__), $size);
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters');
    }

    public function testUploadLocalDetectMime()
    {
        if (! ini_get('file_uploads')) {
            $this->markTestSkipped('File uploads disabled.');
        }

        $detect = null;
        if (function_exists('finfo_file')) {
            $f = @finfo_open(FILEINFO_MIME);
            if ($f) {
                $detect = 'finfo';
            }
        } elseif (function_exists('mime_content_type')) {
            if (mime_content_type(__FILE__)) {
                $detect = 'mime_magic';
            }
        }

        if (! $detect) {
            $this->markTestSkipped(
                'No MIME type detection capability (fileinfo or mime_magic extensions) is available'
            );
        }

        $file = __DIR__ . '/_files/staticFile.jpg';

        $this->client->setUri($this->baseuri . 'testUploads.php');
        $this->client->setFileUpload($file, 'uploadfile');
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $size = filesize($file);
        $body = sprintf('uploadfile %s image/jpeg %d' . "\n", basename($file), $size);
        $this->assertEquals(
            $body,
            $res->getBody(),
            'Response body does not include expected upload parameters (detect: ' . $detect . ')'
        );
    }

    public function testUploadNameWithSpecialChars()
    {
        if (! ini_get('file_uploads')) {
            $this->markTestSkipped('File uploads disabled.');
        }

        $this->client->setUri($this->baseuri . 'testUploads.php');

        $rawdata = file_get_contents(__FILE__);
        $this->client->setFileUpload('/some strage/path%/with[!@#$&]/myfile.txt', 'uploadfile', $rawdata, 'text/plain');
        $this->client->setMethod('POST');
        $res = $this->client->send();

        $body = 'uploadfile myfile.txt text/plain ' . strlen($rawdata) . "\n";
        $this->assertEquals($body, $res->getBody(), 'Response body does not include expected upload parameters');
    }

    public function testStaticLargeFileDownload()
    {
        $this->client->setUri($this->baseuri . 'staticFile.jpg');

        $got      = $this->client->send()->getBody();
        $expected = $this->getTestFileContents('staticFile.jpg');

        $this->assertEquals($expected, $got, 'Downloaded file does not seem to match!');
    }

    /**
     * Test that one can upload multiple files with the same form name, as an
     * array
     *
     * @link https://getlaminas.org/issues/browse/Laminas-5744
     */
    public function testMultipleFilesWithSameFormNameLaminas5744()
    {
        if (! ini_get('file_uploads')) {
            $this->markTestSkipped('File uploads disabled.');
        }

        $rawData = 'Some test raw data here...';

        $this->client->setUri($this->baseuri . 'testUploads.php');

        $files = ['file1.txt', 'file2.txt', 'someotherfile.foo'];

        $expectedBody = '';
        foreach ($files as $filename) {
            $this->client->setFileUpload($filename, 'uploadfile[]', $rawData, 'text/plain');
            $expectedBody .= sprintf('uploadfile %s text/plain %d' . "\n", $filename, strlen($rawData));
        }
        $this->client->setMethod('POST');

        $res = $this->client->send();

        $this->assertEquals(
            $expectedBody,
            $res->getBody(),
            'Response body does not include expected upload parameters'
        );
    }

    /**
     * Test that lines that might be evaluated as boolean false do not break
     * the reading prematurely.
     *
     * @group Laminas-4238
     */
    public function testLaminas4238FalseLinesInResponse()
    {
        $this->client->setUri($this->baseuri . 'Laminas4238-zerolineresponse.txt');

        $got      = $this->client->send()->getBody();
        $expected = $this->getTestFileContents('Laminas4238-zerolineresponse.txt');
        $this->assertEquals($expected, $got);
    }

    public function testStreamResponse()
    {
        if (! $this->client->getAdapter() instanceof Adapter\StreamInterface) {
            $this->markTestSkipped('Current adapter does not support streaming');
            return;
        }
        $this->client->setUri($this->baseuri . 'staticFile.jpg');
        $this->client->setStream();

        $response = $this->client->send();

        $this->assertInstanceOf(Stream::class, $response, 'Request did not return stream response!');
        $this->assertIsResource($response->getStream(), 'Request does not contain stream!');

        $streamName = $response->getStreamName();

        $streamRead = stream_get_contents($response->getStream());
        $fileRead   = file_get_contents($streamName);

        $expected = $this->getTestFileContents('staticFile.jpg');

        $this->assertEquals($expected, $streamRead, 'Downloaded stream does not seem to match!');
        $this->assertEquals($expected, $fileRead, 'Downloaded file does not seem to match!');
    }

    public function testStreamResponseBody()
    {
        $this->markTestSkipped('To check with the new Laminas implementation');

        if (! $this->client->getAdapter() instanceof Adapter\StreamInterface) {
            $this->markTestSkipped('Current adapter does not support streaming');
            return;
        }
        $this->client->setUri($this->baseuri . 'staticFile.jpg');
        $this->client->setStream();

        $response = $this->client->send();

        $this->assertInstanceOf(Stream::class, $response, 'Request did not return stream response!');
        $this->assertIsResource($response->getStream(), 'Request does not contain stream!');

        $body = $response->getBody();

        $expected = $this->getTestFileContents('staticFile.jpg');
        $this->assertEquals($expected, $body, 'Downloaded stream does not seem to match!');
    }

    public function testStreamResponseNamed()
    {
        if (! $this->client->getAdapter() instanceof Adapter\StreamInterface) {
            $this->markTestSkipped('Current adapter does not support streaming');
            return;
        }
        $this->client->setUri($this->baseuri . 'staticFile.jpg');
        $outfile = tempnam(sys_get_temp_dir(), 'outstream');
        $this->client->setStream($outfile);

        $response = $this->client->send();

        $this->assertInstanceOf(Stream::class, $response, 'Request did not return stream response!');
        $this->assertIsResource($response->getStream(), 'Request does not contain stream!');

        $this->assertEquals($outfile, $response->getStreamName());

        $streamRead = stream_get_contents($response->getStream());
        $fileRead   = file_get_contents($outfile);

        $expected = $this->getTestFileContents('staticFile.jpg');

        $this->assertEquals($expected, $streamRead, 'Downloaded stream does not seem to match!');
        $this->assertEquals($expected, $fileRead, 'Downloaded file does not seem to match!');
    }

    public function testStreamRequest()
    {
        if (! $this->client->getAdapter() instanceof Adapter\StreamInterface) {
            $this->markTestSkipped('Current adapter does not support streaming');
            return;
        }
        $data = fopen(__DIR__ . '/_files/staticFile.jpg', 'r');
        $this->client->setRawBody($data);
        $this->client->setEncType('image/jpeg');
        $this->client->setMethod('PUT');
        $res      = $this->client->send();
        $expected = $this->getTestFileContents('staticFile.jpg');
        $this->assertEquals($expected, $res->getBody(), 'Response body does not contain the expected data');
    }

    /**
     * Test that we can deal with double Content-Length headers
     *
     * @link https://getlaminas.org/issues/browse/Laminas-9404
     */
    public function testLaminas9404DoubleContentLengthHeader()
    {
        $this->client->setUri($this->baseuri . 'Laminas9404-doubleContentLength.php');
        $expect = filesize(__DIR__ . '/_files/Laminas9404-doubleContentLength.php');

        $response = $this->client->send();
        if (! $response->isSuccess()) {
            throw new AdapterException\RuntimeException('Error requesting test URL');
        }

        $clen = $response->getHeaders()->get('Content-Length');

        if (! is_array($clen)) {
            $this->markTestSkipped("Didn't get multiple Content-length headers");
        }

        $this->assertEquals($expect, strlen($response->getBody()));
    }

    /**
     * @group Laminas-78
     * @dataProvider parameterArrayProvider
     * @param array $params
     */
    public function testContentTypeAdditionlInfo(array $params)
    {
        $contentType = 'application/x-www-form-urlencoded; charset=UTF-8';

        $this->client->setUri($this->baseuri . 'testPostData.php');
        $this->client->setHeaders([
            'Content-Type' => $contentType,
        ]);
        $this->client->setMethod(Request::METHOD_POST);

        $this->client->setParameterPost($params);

        $this->client->send();
        $request = Request::fromString($this->client->getLastRawRequest());
        $this->assertEquals(
            $contentType,
            $request->getHeaders()->get('Content-Type')->getFieldValue()
        );
    }

    /**
     * @group 2774
     * @group 2745
     */
    public function testUsesProvidedArgSeparator()
    {
        $this->client->setArgSeparator(';');
        $request = new Request();
        $request->setUri('https://getlaminas.org');
        $request->setQuery(new Parameters(['foo' => 'bar', 'baz' => 'bat']));
        $this->client->send($request);
        $rawRequest = $this->client->getLastRawRequest();
        $this->assertStringContainsString('?foo=bar;baz=bat', $rawRequest);
    }

    /**
     * Internal helper function to get the contents of test files
     *
     * @param  string $file
     * @return string
     */
    private function getTestFileContents($file)
    {
        return file_get_contents(__DIR__ . '/_files/' . $file);
    }

    /**
     * Data provider for complex, nesting parameter arrays
     *
     * @return array
     */
    public static function parameterArrayProvider()
    {
        return [
            [
                [
                    'quest'        => 'To seek the holy grail',
                    'YourMother'   => 'Was a hamster',
                    'specialChars' => '<>$+ &?=[]^%',
                    'array'        => ['firstItem', 'secondItem', '3rdItem'],
                ],
            ],
            [
                [
                    'someData'      => [
                        '1',
                        '2',
                        'key'     => 'value',
                        'nesting' => [
                            'a' => 'AAA',
                            'b' => 'BBB',
                        ],
                    ],
                    'someOtherData' => ['foo', 'bar'],
                ],
            ],
            [
                [
                    'foo1' => 'bar',
                    'foo2' => ['baz', 'w00t'],
                ],
            ],
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

    /**
     * Get an URI that does not accept HTTP connections.
     *
     * @return string
     */
    protected function getNotRespondingUri()
    {
        $notRespondingUri = getenv('TESTS_LAMINAS_HTTP_CLIENT_NOTRESPONDINGURI');
        if (! $notRespondingUri) {
            $notRespondingUri = 'http://192.168.10.250:65530/';
        }

        return $notRespondingUri;
    }

    /**
     * Check connecttimeout/timeout: invalid URIs should timeout after 'connecttimeout' seconds.
     */
    public function testConnectTimeout1()
    {
        $connectTimeout = 1;
        $executeTimeout = 5;
        $this->client
            ->setUri($this->getNotRespondingUri())
            ->setMethod('GET')
            ->setOptions([
                'connecttimeout' => $connectTimeout,
                'timeout'        => $executeTimeout,
            ]);
        $timeoutException = null;
        $startTime        = microtime(true);
        try {
            $this->client->send();
        } catch (Exception $x) {
            $timeoutException = $x;
        }
        $endTime = microtime(true);
        if ($timeoutException === null) {
            $this->markTestSkipped(sprintf(
                'There is something responding at %s',
                $this->getNotRespondingUri()
            ));
        }
        $deltaTime = ceil($endTime - $startTime);
        $this->assertGreaterThanOrEqual($connectTimeout, $deltaTime);
        $this->assertLessThan($executeTimeout, $deltaTime);
    }

    /**
     * Check connecttimeout/timeout: valid but slow URIs should timeout after 'timeout' seconds.
     */
    public function testConnectTimeout2()
    {
        $connectTimeout = 1;
        $executeTimeout = 2;
        $this->client
            ->setUri($this->baseuri . 'testConnectTimeout.php')
            ->setMethod('GET')
            ->setOptions([
                'connecttimeout' => $connectTimeout,
                'timeout'        => $executeTimeout,
            ]);
        $timeoutException = null;
        $startTime        = microtime(true);
        try {
            $this->client->send();
        } catch (Exception $x) {
            $timeoutException = $x;
        }
        $endTime = microtime(true);
        $this->assertNotNull($timeoutException, 'The request should timeout');
        $deltaTime = ceil($endTime - $startTime);
        $this->assertGreaterThanOrEqual($executeTimeout, $deltaTime);
    }
}
