<?php

declare(strict_types=1);

namespace LaminasTest\Http;

use Laminas\Http\Exception\InvalidArgumentException;
use Laminas\Http\Exception\RuntimeException;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\Stdlib\Parameters;
use Laminas\Uri\Uri;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

use function str_starts_with;
use function strtolower;

class RequestTest extends TestCase
{
    public function testRequestFromStringFactoryCreatesValidRequest(): void
    {
        $string  = "GET /foo?myparam=myvalue HTTP/1.1\r\n\r\nSome Content";
        $request = Request::fromString($string);

        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('/foo?myparam=myvalue', $request->getUri());
        $this->assertEquals('myvalue', $request->getQuery()->get('myparam'));
        $this->assertEquals(Request::VERSION_11, $request->getVersion());
        $this->assertEquals('Some Content', $request->getContent());
    }

    public function testRequestUsesParametersContainerByDefault(): void
    {
        $request = new Request();
        $this->assertInstanceOf(Parameters::class, $request->getQuery());
        $this->assertInstanceOf(Parameters::class, $request->getPost());
        $this->assertInstanceOf(Parameters::class, $request->getFiles());
    }

    public function testRequestAllowsSettingOfParameterContainer(): void
    {
        $request = new Request();
        $p       = new Parameters();
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $this->assertSame($p, $request->getQuery());
        $this->assertSame($p, $request->getPost());
        $this->assertSame($p, $request->getFiles());

        $headers = new Headers();
        $request->setHeaders($headers);
        $this->assertSame($headers, $request->getHeaders());
    }

    public function testRetrievingASingleValueForParameters(): void
    {
        $request = new Request();
        $p       = new Parameters([
            'foo' => 'bar',
        ]);
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $this->assertSame('bar', $request->getQuery('foo'));
        $this->assertSame('bar', $request->getPost('foo'));
        $this->assertSame('bar', $request->getFiles('foo'));

        $headers = new Headers();
        $h       = new GenericHeader('foo', 'bar');
        $headers->addHeader($h);

        $request->setHeaders($headers);
        $this->assertSame($headers, $request->getHeaders());
        $this->assertSame($h, $request->getHeaders()->get('foo'));
        $this->assertSame($h, $request->getHeader('foo'));
    }

    public function testParameterRetrievalDefaultValue(): void
    {
        $request = new Request();
        $p       = new Parameters([
            'foo' => 'bar',
        ]);
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $default = 15;
        $this->assertSame($default, $request->getQuery('baz', $default));
        $this->assertSame($default, $request->getPost('baz', $default));
        $this->assertSame($default, $request->getFiles('baz', $default));
        $this->assertSame($default, $request->getHeaders('baz', $default));
        $this->assertSame($default, $request->getHeader('baz', $default));
    }

    public function testRequestPersistsRawBody(): void
    {
        $request = new Request();
        $request->setContent('foo');
        $this->assertEquals('foo', $request->getContent());
    }

    public function testRequestUsesHeadersContainerByDefault(): void
    {
        $request = new Request();
        $this->assertInstanceOf(Headers::class, $request->getHeaders());
    }

    public function testRequestCanSetHeaders(): void
    {
        $request = new Request();
        $headers = new Headers();

        $ret = $request->setHeaders($headers);
        $this->assertInstanceOf(Request::class, $ret);
        $this->assertSame($headers, $request->getHeaders());
    }

    public function testRequestCanSetAndRetrieveValidMethod(): void
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testRequestCanAlwaysForcesUppecaseMethodName(): void
    {
        $request = new Request();
        $request->setMethod('get');
        $this->assertEquals('GET', $request->getMethod());
    }

    #[DataProvider('uriDataProvider')]
    public function testRequestCanSetAndRetrieveUri(string $uri): void
    {
        $request = new Request();
        $request->setUri($uri);
        $this->assertEquals($uri, $request->getUri());
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertEquals($uri, $request->getUri()->toString());
        $this->assertEquals($uri, $request->getUriString());
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public static function uriDataProvider(): array
    {
        return [
            ['/foo'],
            ['/foo#test'],
            ['/hello?what=true#noway'],
        ];
    }

    public function testRequestSetUriWillThrowExceptionOnInvalidArgument(): void
    {
        $request = new Request();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be an instance of');
        $request->setUri(new stdClass());
    }

    public function testRequestCanSetAndRetrieveVersion(): void
    {
        $request = new Request();
        $this->assertEquals('1.1', $request->getVersion());
        $request->setVersion(Request::VERSION_10);
        $this->assertEquals('1.0', $request->getVersion());
    }

    public function testRequestSetVersionWillThrowExceptionOnInvalidArgument(): void
    {
        $request = new Request();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not valid or not supported HTTP version');
        $request->setVersion('1.2');
    }

    #[DataProvider('getMethodsProvider')]
    public function testRequestMethodCheckWorksForAllMethods(string $methodName): void
    {
        $request = new Request();
        $request->setMethod($methodName);

        foreach ($this->getMethods($methodName) as $testMethodName => $testMethodValue) {
            $this->assertEquals($testMethodValue, $request->{'is' . $testMethodName}());
        }
    }

    public function testRequestCanBeCastToAString(): void
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri('/');
        $request->setContent('foo=bar&bar=baz');
        $this->assertEquals("GET / HTTP/1.1\r\n\r\nfoo=bar&bar=baz", $request->toString());
    }

    public function testRequestIsXmlHttpRequest(): void
    {
        $request = new Request();
        $this->assertFalse($request->isXmlHttpRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'FooBazBar');
        $this->assertFalse($request->isXmlHttpRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->assertTrue($request->isXmlHttpRequest());
    }

    public function testRequestIsFlashRequest(): void
    {
        $request = new Request();
        $this->assertFalse($request->isFlashRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('USER_AGENT', 'FooBazBar');
        $this->assertFalse($request->isFlashRequest());

        $request = new Request();
        $request->getHeaders()->addHeaderLine('USER_AGENT', 'Shockwave Flash');
        $this->assertTrue($request->isFlashRequest());
    }

    #[Group('4893')]
    public function testRequestsWithoutHttpVersionAreOK(): void
    {
        $requestString = 'GET http://www.domain.com/index.php';
        $request       = Request::fromString($requestString);
        $this->assertEquals($request::METHOD_GET, $request->getMethod());
    }

    /** @return array<string, bool> */
    private static function getMethods(string $trueMethod): array
    {
        $return = [];
        foreach (self::getMethodsConstOnRequest() as $cValue) {
            $return[strtolower($cValue)] = $trueMethod === $cValue;
        }
        return $return;
    }

    /** @return list<string> */
    private static function getMethodsConstOnRequest(): array
    {
        $refClass = new ReflectionClass(Request::class);
        $return   = [];
        foreach ($refClass->getConstants() as $cName => $cValue) {
            if (str_starts_with($cName, 'METHOD')) {
                $return[] = $cValue;
            }
        }

        return $return;
    }

    /** @return list<list<string>> */
    public static function getMethodsProvider(): array
    {
        $return = [];
        foreach (self::getMethodsConstOnRequest() as $cValue) {
            $return[] = [$cValue];
        }
        return $return;
    }

    public function testCustomMethods(): void
    {
        $request = new Request();
        $this->assertTrue($request->getAllowCustomMethods());
        $request->setMethod('xcustom');

        $this->assertEquals('XCUSTOM', $request->getMethod());
    }

    public function testDisallowCustomMethods(): void
    {
        $request = new Request();
        $request->setAllowCustomMethods(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP method passed');

        $request->setMethod('xcustom');
    }

    public function testCustomMethodsFromString(): void
    {
        $request = Request::fromString('X-CUS_TOM someurl');
        $this->assertTrue($request->getAllowCustomMethods());

        $this->assertEquals('X-CUS_TOM', $request->getMethod());
    }

    public function testDisallowCustomMethodsFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A valid request line was not found in the provided string');

        Request::fromString('X-CUS_TOM someurl', false);
    }

    public function testAllowCustomMethodsFlagIsSetByFromString(): void
    {
        $request = Request::fromString('GET someurl', false);
        $this->assertFalse($request->getAllowCustomMethods());
    }

    public function testFromStringFactoryCreatesSingleObjectWithHeaderFolding(): void
    {
        $request = Request::fromString("GET /foo HTTP/1.1\r\nFake: foo\r\n -bar");
        $headers = $request->getHeaders();
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf(GenericHeader::class, $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testCRLFAttack(): void
    {
        $this->expectException(RuntimeException::class);
        Request::fromString(
            "GET /foo HTTP/1.1\r\nHost: example.com\r\nX-Foo: This\ris\r\n\r\nCRLF\nInjection"
        );
    }

    public function testGetHeadersDoesNotRaiseExceptionForInvalidHeaderLines(): void
    {
        $request = Request::fromString("GET /foo HTTP/1.1\r\nHost: example.com\r\nUseragent: h4ckerbot");

        $headers = $request->getHeaders();
        $this->assertFalse($headers->has('User-Agent'));
        $this->assertFalse($headers->get('User-Agent'));
        $this->assertSame('bar-baz', $request->getHeader('User-Agent', 'bar-baz'));

        $this->assertTrue($headers->has('useragent'));
        $this->assertInstanceOf(GenericHeader::class, $headers->get('useragent'));
        $this->assertSame('h4ckerbot', $headers->get('useragent')->getFieldValue());
        $this->assertSame('h4ckerbot', $request->getHeader('useragent')->getFieldValue());
    }
}
