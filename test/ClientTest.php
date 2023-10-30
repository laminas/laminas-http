<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps

namespace LaminasTest\Http;

use ArrayIterator;
use Laminas\Http\Client;
use Laminas\Http\Client\Adapter\AdapterInterface;
use Laminas\Http\Client\Adapter\Curl;
use Laminas\Http\Client\Adapter\Proxy;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Http\Client\Adapter\Test;
use Laminas\Http\Client\Exception as ClientException;
use Laminas\Http\Cookies;
use Laminas\Http\Exception as HttpException;
use Laminas\Http\Header\AcceptEncoding;
use Laminas\Http\Header\SetCookie;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Uri\Http;
use LaminasTest\Http\TestAsset\ExtendedClient;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

use function base64_encode;
use function count;
use function file_get_contents;
use function ini_get;
use function ini_set;
use function json_encode;
use function strlen;
use function sys_get_temp_dir;
use function tempnam;

class ClientTest extends TestCase
{
    public function testIfCookiesAreSticky()
    {
        $initialCookies = [
            new SetCookie('foo', 'far', null, '/', 'www.domain.com'),
            new SetCookie('bar', 'biz', null, '/', 'www.domain.com'),
        ];

        $requestString = 'GET http://www.domain.com/index.php HTTP/1.1' . "\r\n"
            . 'Host: domain.com' . "\r\n"
            . 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:16.0) Gecko/20100101 Firefox/16.0' . "\r\n"
            . 'Accept: */*' . "\r\n"
            . 'Accept-Language: en-US,en;q=0.5' . "\r\n"
            . 'Accept-Encoding: gzip, deflate' . "\r\n"
            . 'Connection: keep-alive' . "\r\n";
        $request       = Request::fromString($requestString);

        $client = new Client('http://www.domain.com/');
        $client->setRequest($request);
        $client->addCookie($initialCookies);

        $cookies    = new Cookies($client->getRequest()->getHeaders());
        $rawHeaders = 'HTTP/1.1 200 OK' . "\r\n"
            . 'Access-Control-Allow-Origin: *' . "\r\n"
            . 'Content-Encoding: gzip' . "\r\n"
            . 'Content-Type: application/javascript' . "\r\n"
            . 'Date: Sun, 18 Nov 2012 16:16:08 GMT' . "\r\n"
            . 'Server: nginx/1.1.19' . "\r\n"
            . 'Set-Cookie: baz=bah; domain=www.domain.com; path=/' . "\r\n"
            . 'Set-Cookie: joe=test; domain=www.domain.com; path=/' . "\r\n"
            . 'Vary: Accept-Encoding' . "\r\n"
            . 'X-Powered-By: PHP/5.3.10-1ubuntu3.4' . "\r\n"
            . 'Connection: keep-alive' . "\r\n";
        $response   = Response::fromString($rawHeaders);
        $client->setResponse($response);

        $cookies->addCookiesFromResponse($client->getResponse(), $client->getUri());

        $client->addCookie($cookies->getMatchingCookies($client->getUri()));

        $this->assertEquals(4, count($client->getCookies()));
    }

    public function testClientRetrievesUppercaseHttpMethodFromRequestObject()
    {
        $client = new Client();
        $client->setMethod('post');
        $this->assertEquals(Client::ENC_URLENCODED, $client->getEncType());
    }

    public function testAcceptEncodingHeaderWorksProperly()
    {
        $method = new ReflectionMethod(Client::class, 'prepareHeaders');
        $method->setAccessible(true);

        $requestString = 'GET http://www.domain.com/index.php HTTP/1.1' . "\r\n"
            . 'Host: domain.com' . "\r\n"
            . 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:16.0) Gecko/20100101 Firefox/16.0' . "\r\n"
            . 'Accept: */*' . "\r\n"
            . 'Accept-Language: en-US,en;q=0.5' . "\r\n"
            . 'Accept-Encoding: gzip, deflate' . "\r\n"
            . 'Connection: keep-alive' . "\r\n";
        $request       = Request::fromString($requestString);

        $adapter = new Test();

        $client = new Client('http://www.domain.com/');
        $client->setAdapter($adapter);
        $client->setRequest($request);

        $rawHeaders = 'HTTP/1.1 200 OK' . "\r\n"
            . 'Access-Control-Allow-Origin: *' . "\r\n"
            . 'Content-Encoding: gzip, deflate' . "\r\n"
            . 'Content-Type: application/javascript' . "\r\n"
            . 'Date: Sun, 18 Nov 2012 16:16:08 GMT' . "\r\n"
            . 'Server: nginx/1.1.19' . "\r\n"
            . 'Vary: Accept-Encoding' . "\r\n"
            . 'X-Powered-By: PHP/5.3.10-1ubuntu3.4' . "\r\n"
            . 'Connection: keep-alive' . "\r\n";
        $response   = Response::fromString($rawHeaders);
        $client->getAdapter()->setResponse($response);

        $headers = $method->invoke($client, $requestString, $client->getUri());
        $this->assertEquals('gzip, deflate', $headers['Accept-Encoding']);
    }

    public function testIfZeroValueCookiesCanBeSet()
    {
        $client = new Client();
        $client->addCookie('test', 0);
        $client->addCookie('test2', '0');
        $client->addCookie('test3', false);
    }

    public function testIfNullValueCookiesThrowsException()
    {
        $client = new Client();

        $this->expectException(HttpException\InvalidArgumentException::class);
        $client->addCookie('test', null);
    }

    public function testIfCookieHeaderCanBeSet()
    {
        $header = [new SetCookie('foo', 'bar')];
        $client = new Client();
        $client->addCookie($header);

        $cookies = $client->getCookies();
        $this->assertEquals(1, count($cookies));
        $this->assertEquals($header[0], $cookies['foo']);
    }

    public function testIfArrayOfHeadersCanBeSet()
    {
        $headers = [
            new SetCookie('foo'),
            new SetCookie('bar'),
        ];

        $client = new Client();
        $client->addCookie($headers);

        $cookies = $client->getCookies();
        $this->assertEquals(2, count($cookies));
    }

    public function testIfArrayIteratorOfHeadersCanBeSet()
    {
        $headers = new ArrayIterator([
            new SetCookie('foo'),
            new SetCookie('bar'),
        ]);

        $client = new Client();
        $client->addCookie($headers);

        $cookies = $client->getCookies();
        $this->assertEquals(2, count($cookies));
    }

    /**
     * @group 2774
     * @group 2745
     */
    public function testArgSeparatorDefaultsToIniSetting()
    {
        $argSeparator = ini_get('arg_separator.output');
        $client       = new Client();
        $this->assertEquals($argSeparator, $client->getArgSeparator());
    }

    /**
     * @group 2774
     * @group 2745
     */
    public function testCanOverrideArgSeparator()
    {
        $client = new Client();
        $client->setArgSeparator(';');
        $this->assertEquals(';', $client->getArgSeparator());
    }

    public function testClientUsesAcceptEncodingHeaderFromRequestObject()
    {
        $client = new Client();

        $client->setAdapter(Test::class);

        $request = $client->getRequest();

        $acceptEncodingHeader = new AcceptEncoding();
        $acceptEncodingHeader->addEncoding('foo', 1);
        $request->getHeaders()->addHeader($acceptEncodingHeader);

        $client->send();

        $rawRequest = $client->getLastRawRequest();

        $this->assertStringNotContainsString('Accept-Encoding: gzip, deflate', $rawRequest, '', true);
        $this->assertStringNotContainsString('Accept-Encoding: identity', $rawRequest, '', true);

        $this->assertStringContainsString('Accept-Encoding: foo', $rawRequest);
    }

    public function testEncodeAuthHeaderWorksAsExpected()
    {
        $encoded = Client::encodeAuthHeader('test', 'test');
        $this->assertEquals('Basic ' . base64_encode('test:test'), $encoded);
    }

    public function testEncodeAuthHeaderThrowsExceptionWhenUsernameContainsSemiColon()
    {
        $this->expectException(ClientException\InvalidArgumentException::class);
        Client::encodeAuthHeader('test:', 'test');
    }

    public function testEncodeAuthHeaderThrowsExceptionWhenInvalidAuthTypeIsUsed()
    {
        $this->expectException(ClientException\InvalidArgumentException::class);
        Client::encodeAuthHeader('test', 'test', 'test');
    }

    public function testIfMaxredirectWorksCorrectly()
    {
        $testAdapter = new Test();
        // first response, contains a redirect
        $testAdapter->setResponse(
            'HTTP/1.1 303 See Other' . "\r\n"
            . 'Location: http://www.example.org/part2' . "\r\n\r\n"
            . 'Page #1'
        );
        // seconds response, contains a redirect
        $testAdapter->addResponse(
            'HTTP/1.1 303 See Other' . "\r\n"
            . 'Location: http://www.example.org/part3' . "\r\n\r\n"
            . 'Page #2'
        );
        // third response
        $testAdapter->addResponse(
            'HTTP/1.1 303 See Other' . "\r\n\r\n"
            . 'Page #3'
        );

        // create a client which allows one redirect at most!
        $client = new Client('http://www.example.org/part1', [
            'adapter'       => $testAdapter,
            'maxredirects'  => 1,
            'storeresponse' => true,
        ]);

        // do the request
        $response = $client->setMethod('GET')->send();

        // response should be the second response, since third response should not
        // be requested, due to the maxredirects = 1 limit
        $this->assertEquals($response->getContent(), 'Page #2');
    }

    public function testIfClientDoesNotLooseAuthenticationOnRedirect()
    {
        // set up user credentials
        $user     = 'username123';
        $password = 'password456';
        $encoded  = Client::encodeAuthHeader($user, $password, Client::AUTH_BASIC);

        // set up two responses that simulate a redirection
        $testAdapter = new Test();
        $testAdapter->setResponse(
            'HTTP/1.1 303 See Other' . "\r\n"
            . 'Location: http://www.example.org/part2' . "\r\n\r\n"
            . 'The URL of this page has changed.'
        );
        $testAdapter->addResponse(
            'HTTP/1.1 200 OK' . "\r\n\r\n"
            . 'Welcome to this Website.'
        );

        // create client with HTTP basic authentication
        $client = new Client('http://www.example.org/part1', [
            'adapter'      => $testAdapter,
            'maxredirects' => 1,
        ]);
        $client->setAuth($user, $password, Client::AUTH_BASIC);

        // do request
        $client->setMethod('GET')->send();

        // the last request should contain the Authorization header
        $this->assertStringContainsString($encoded, $client->getLastRawRequest());
    }

    public function testIfClientDoesNotForwardAuthenticationToForeignHost()
    {
        // set up user credentials
        $user     = 'username123';
        $password = 'password456';
        $encoded  = Client::encodeAuthHeader($user, $password, Client::AUTH_BASIC);

        $testAdapter = new Test();
        $client      = new Client(null, ['adapter' => $testAdapter]);

        // set up two responses that simulate a redirection from example.org to example.com
        $testAdapter->setResponse(
            'HTTP/1.1 303 See Other' . "\r\n"
            . 'Location: http://example.com/part2' . "\r\n\r\n"
            . 'The URL of this page has changed.'
        );
        $testAdapter->addResponse(
            'HTTP/1.1 200 OK' . "\r\n\r\n"
            . 'Welcome to this Website.'
        );

        // set auth and do request
        $client->setUri('http://example.org/part1')
            ->setAuth($user, $password, Client::AUTH_BASIC);
        $client->setMethod('GET')->send();

        // the last request should NOT contain the Authorization header,
        // because example.com is different from example.org
        $this->assertStringNotContainsString($encoded, $client->getLastRawRequest());

        // set up two responses that simulate a redirection from example.org to sub.example.org
        $testAdapter->setResponse(
            'HTTP/1.1 303 See Other' . "\r\n"
            . 'Location: http://sub.example.org/part2' . "\r\n\r\n"
            . 'The URL of this page has changed.'
        );
        $testAdapter->addResponse(
            'HTTP/1.1 200 OK' . "\r\n\r\n"
            . 'Welcome to this Website.'
        );

        // set auth and do request
        $client->setUri('http://example.org/part1')
            ->setAuth($user, $password, Client::AUTH_BASIC);
        $client->setMethod('GET')->send();

        // the last request should contain the Authorization header,
        // because sub.example.org is a subdomain unter example.org
        $this->assertStringContainsString($encoded, $client->getLastRawRequest());

        // set up two responses that simulate a rediration from sub.example.org to example.org
        $testAdapter->setResponse(
            'HTTP/1.1 303 See Other' . "\r\n"
            . 'Location: http://example.org/part2' . "\r\n\r\n"
            . 'The URL of this page has changed.'
        );
        $testAdapter->addResponse(
            'HTTP/1.1 200 OK' . "\r\n\r\n"
            . 'Welcome to this Website.'
        );

        // set auth and do request
        $client->setUri('http://sub.example.org/part1')
            ->setAuth($user, $password, Client::AUTH_BASIC);
        $client->setMethod('GET')->send();

        // the last request should NOT contain the Authorization header,
        // because example.org is not a subdomain unter sub.example.org
        $this->assertStringNotContainsString($encoded, $client->getLastRawRequest());
    }

    public function testAdapterAlwaysReachableIfSpecified()
    {
        $testAdapter = new Test();
        $client      = new Client('http://www.example.org/', [
            'adapter' => $testAdapter,
        ]);

        $this->assertSame($testAdapter, $client->getAdapter());
    }

    public function testPrepareHeadersCreateRightHttpField()
    {
        $body = json_encode(['foofoo' => 'barbar']);

        $client                   = new Client();
        $prepareHeadersReflection = new ReflectionMethod($client, 'prepareHeaders');
        $prepareHeadersReflection->setAccessible(true);

        $request = new Request();
        $request->getHeaders()->addHeaderLine('content-type', 'application/json');
        $request->getHeaders()->addHeaderLine('content-length', strlen($body));
        $client->setRequest($request);

        $client->setEncType('application/json');

        $this->assertSame($client->getRequest(), $request);

        $headers = $prepareHeadersReflection->invoke($client, $body, new Http('http://localhost:5984'));

        $this->assertArrayNotHasKey('content-type', $headers);
        $this->assertArrayHasKey('Content-Type', $headers);

        $this->assertArrayNotHasKey('content-length', $headers);
        $this->assertArrayHasKey('Content-Length', $headers);
    }

    public function testPrepareHeadersCurlDigestAuthentication()
    {
        $body = json_encode(['foofoo' => 'barbar']);

        $client                   = new Client();
        $prepareHeadersReflection = new ReflectionMethod($client, 'prepareHeaders');
        $prepareHeadersReflection->setAccessible(true);

        $request = new Request();
        $request->getHeaders()->addHeaderLine('Authorization: Digest');
        $request->getHeaders()->addHeaderLine('content-type', 'application/json');
        $request->getHeaders()->addHeaderLine('content-length', strlen($body));
        $client->setRequest($request);

        $this->assertSame($client->getRequest(), $request);

        $headers = $prepareHeadersReflection->invoke($client, $body, new Http('http://localhost:5984'));

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertStringContainsString('Digest', $headers['Authorization']);
    }

    /**
     * @group 6301
     */
    public function testCanSpecifyCustomAuthMethodsInExtendingClasses()
    {
        $client = new ExtendedClient();

        $client->setAuth('username', 'password', ExtendedClient::AUTH_CUSTOM);

        $reflectedProperty = new ReflectionProperty($client, 'auth');
        $reflectedProperty->setAccessible(true);
        $customAuth = $reflectedProperty->getValue($client);

        $this->assertEquals(
            [
                'user'     => 'username',
                'password' => 'password',
                'type'     => ExtendedClient::AUTH_CUSTOM,
            ],
            $customAuth
        );
    }

    /**
     * @group 6231
     */
    public function testHttpQueryParametersCastToString()
    {
        $client = new Client();

        $adapter = $this->createMock(AdapterInterface::class);

        $client->setAdapter($adapter);

        $request = new Request();

        $request->setUri('http://example.com/');
        $request->getQuery()->set('foo', 'bar');

        $response = new Response();

        $adapter
            ->expects($this->once())
            ->method('write')
            ->with(Request::METHOD_GET, 'http://example.com/?foo=bar');

        $adapter
            ->expects($this->any())
            ->method('read')
            ->will($this->returnValue($response->toString()));

        $client->send($request);
    }

    /**
     * @group 6959
     */
    public function testClientRequestMethod()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->getPost()->set('data', 'random');

        $client = new Client();
        $client->setAdapter(Test::class);
        $client->send($request);

        $this->assertSame(Client::ENC_URLENCODED, $client->getEncType());
    }

    public function testClientEmptyPostPut()
    {
        $client                   = new Client();
        $prepareHeadersReflection = new ReflectionMethod($client, 'prepareHeaders');
        $prepareHeadersReflection->setAccessible(true);
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $client->setRequest($request);
        $this->assertSame($client->getRequest(), $request);
        $headers = $prepareHeadersReflection->invoke($client, '', new Http('http://localhost:5984'));
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Content-Length', $headers);
        $this->assertSame($headers['Content-Length'], 0);
        $request = new Request();
        $request->setMethod(Request::METHOD_PUT);
        $client->setRequest($request);
        $this->assertSame($client->getRequest(), $request);
        $headers = $prepareHeadersReflection->invoke($client, '', new Http('http://localhost:5984'));
        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Content-Length', $headers);
        $this->assertSame($headers['Content-Length'], 0);
    }

    /**
     * @group 7332
     */
    public function testAllowsClearingEncType()
    {
        $client = new Client();
        $client->setEncType('application/x-www-form-urlencoded');

        $this->assertEquals('application/x-www-form-urlencoded', $client->getEncType());

        $client->setEncType(null);
        $this->assertNull($client->getEncType());
    }

    /**
     * @see https://github.com/zendframework/zend-http/issues/33
     */
    public function testFormUrlEncodeSeparator()
    {
        $client = new Client();
        $client->setEncType('application/x-www-form-urlencoded');
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->getPost()->set('foo', 'bar');
        $request->getPost()->set('baz', 'foo');
        ini_set('arg_separator.output', '$');
        $client->setAdapter(Test::class);
        $client->send($request);
        $rawRequest = $client->getLastRawRequest();
        $this->assertStringContainsString('foo=bar&baz=foo', $rawRequest);
    }

    /** @psalm-return array<string, array{0: string, 1: bool}> */
    public function uriDataProvider(): array
    {
        return [
            'valid-relative'   => ['/example', true],
            'invalid-absolute' => ['http://localhost/example', false],
        ];
    }

    /**
     * @dataProvider uriDataProvider
     */
    public function testUriCorrectlyDeterminesWhetherOrNotItIsAValidRelativeUri(
        string $uri,
        bool $isValidRelativeURI
    ): void {
        $client = new Client($uri);
        $this->assertSame($isValidRelativeURI, $client->getUri()->isValidRelative());

        $client->setAdapter(Test::class);
        $client->send();
        $this->assertSame($isValidRelativeURI, $client->getUri()->isValidRelative());
    }

    /** @psalm-return array<string, array{0: string, 1: int}> */
    public function portChangeDataProvider(): array
    {
        return [
            'default-https' => ['https://localhost/example', 443],
            'default-http'  => ['http://localhost/example', 80],
        ];
    }

    /**
     * @dataProvider portChangeDataProvider
     */
    public function testUriPortIsSetToAppropriateDefaultValueWhenAnUriOmittingThePortIsProvided(
        string $absoluteURI,
        int $port
    ): void {
        $client = new Client();
        $client->getUri()->setPort(null);

        $client->setUri($absoluteURI);
        $this->assertSame($port, $client->getUri()->getPort());

        $client->setAdapter(Test::class);
        $client->send();
        $this->assertSame($port, $client->getUri()->getPort());
    }

    public function testUriPortIsNotSetWhenUriIsRelative()
    {
        $client = new Client('/example');
        $this->assertNull($client->getUri()->getPort());

        $client->setAdapter(Test::class);
        $client->send();
        $this->assertNull($client->getUri()->getPort());
    }

    /** @psalm-return iterable<string, array{0: array<string, string>|SetCookie[]}> */
    public function cookies(): iterable
    {
        yield 'name-value' => [['cookie-name' => 'cookie-value']];
        yield 'SetCookie' => [[new SetCookie('cookie-name', 'cookie-value')]];
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.MismatchingCaseSensitivity
     * @dataProvider cookies
     */
    public function testSetCookies(array $cookies)
    {
        $client = new Client();

        $client->setCookies($cookies);

        self::assertCount(1, $client->getCookies());
        self::assertContainsOnlyInstancesOf(SetCookie::class, $client->getCookies());
    }

    public function testSetCookieAcceptOnlyArray()
    {
        $client = new Client();

        $this->expectException(HttpException\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid cookies passed as parameter, it must be an array');
        $client->setCookies(new SetCookie('name', 'value'));
    }

    /**
     * @return AdapterInterface[]
     */
    public function adapterWithStreamSupport()
    {
        yield 'curl' => [new Curl()];
        yield 'proxy' => [new Proxy()];
        yield 'socket' => [new Socket()];
    }

    /**
     * @dataProvider adapterWithStreamSupport
     */
    public function testStreamCompression(AdapterInterface $adapter)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'stream');

        $client = new Client('https://www.gnu.org/licenses/gpl-3.0.txt');
        $client->setAdapter($adapter);
        $client->setStream($tmpFile);
        $client->send();

        $response = $client->getResponse();

        self::assertSame($response->getBody(), file_get_contents($tmpFile));
    }

    public function testDefaultUserAgentDoesNotUseEscapeCharacter()
    {
        $client = new Client();
        $r      = new ReflectionProperty($client, 'config');
        $r->setAccessible(true);
        $config = $r->getValue($client);
        $this->assertIsArray($config);
        $this->assertArrayHasKey('useragent', $config);
        $this->assertSame('Laminas_Http_Client', $config['useragent']);
    }
}
