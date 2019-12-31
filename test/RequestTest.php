<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http;

use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Headers;
use Laminas\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestFromStringFactoryCreatesValidRequest()
    {
        $string = "GET /foo?myparam=myvalue HTTP/1.1\r\n\r\nSome Content";
        $request = Request::fromString($string);

        $this->assertEquals(Request::METHOD_GET, $request->getMethod());
        $this->assertEquals('/foo?myparam=myvalue', $request->getUri());
        $this->assertEquals('myvalue', $request->getQuery()->get('myparam'));
        $this->assertEquals(Request::VERSION_11, $request->getVersion());
        $this->assertEquals('Some Content', $request->getContent());
    }

    public function testRequestUsesParametersContainerByDefault()
    {
        $request = new Request();
        $this->assertInstanceOf('Laminas\Stdlib\Parameters', $request->getQuery());
        $this->assertInstanceOf('Laminas\Stdlib\Parameters', $request->getPost());
        $this->assertInstanceOf('Laminas\Stdlib\Parameters', $request->getFiles());
    }

    public function testRequestAllowsSettingOfParameterContainer()
    {
        $request = new Request();
        $p = new \Laminas\Stdlib\Parameters();
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

    public function testRetrievingASingleValueForParameters()
    {
        $request = new Request();
        $p = new \Laminas\Stdlib\Parameters(array(
            'foo' => 'bar'
        ));
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);

        $this->assertSame('bar', $request->getQuery('foo'));
        $this->assertSame('bar', $request->getPost('foo'));
        $this->assertSame('bar', $request->getFiles('foo'));

        $headers = new Headers();
        $h = new GenericHeader('foo','bar');
        $headers->addHeader($h);

        $request->setHeaders($headers);
        $this->assertSame($headers, $request->getHeaders());
        $this->assertSame($h, $request->getHeaders()->get('foo'));
        $this->assertSame($h, $request->getHeader('foo'));
    }

    public function testParameterRetrievalDefaultValue()
    {
        $request = new Request();
        $p = new \Laminas\Stdlib\Parameters(array(
            'foo' => 'bar'
        ));
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

    public function testRequestPersistsRawBody()
    {
        $request = new Request();
        $request->setContent('foo');
        $this->assertEquals('foo', $request->getContent());
    }

    public function testRequestUsesHeadersContainerByDefault()
    {
        $request = new Request();
        $this->assertInstanceOf('Laminas\Http\Headers', $request->getHeaders());
    }

    public function testRequestCanSetHeaders()
    {
        $request = new Request();
        $headers = new \Laminas\Http\Headers();

        $ret = $request->setHeaders($headers);
        $this->assertInstanceOf('Laminas\Http\Request', $ret);
        $this->assertSame($headers, $request->getHeaders());
    }

    public function testRequestCanSetAndRetrieveValidMethod()
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    public function testRequestCanAlwaysForcesUppecaseMethodName()
    {
        $request = new Request();
        $request->setMethod('get');
        $this->assertEquals('GET', $request->getMethod());
    }

    /**
     * @dataProvider uriDataProvider
     */
    public function testRequestCanSetAndRetrieveUri($uri)
    {
        $request = new Request();
        $request->setUri($uri);
        $this->assertEquals($uri, $request->getUri());
        $this->assertInstanceOf('Laminas\Uri\Uri', $request->getUri());
        $this->assertEquals($uri, $request->getUri()->toString());
        $this->assertEquals($uri, $request->getUriString());
    }

    public function uriDataProvider()
    {
        return array(
            array('/foo'),
            array('/foo#test'),
            array('/hello?what=true#noway')
        );
    }

    public function testRequestSetUriWillThrowExceptionOnInvalidArgument()
    {
        $request = new Request();

        $this->setExpectedException('Laminas\Http\Exception\InvalidArgumentException', 'must be an instance of');
        $request->setUri(new \stdClass());
    }

    public function testRequestCanSetAndRetrieveVersion()
    {
        $request = new Request();
        $this->assertEquals('1.1', $request->getVersion());
        $request->setVersion(Request::VERSION_10);
        $this->assertEquals('1.0', $request->getVersion());
    }

    public function testRequestSetVersionWillThrowExceptionOnInvalidArgument()
    {
        $request = new Request();

        $this->setExpectedException(
            'Laminas\Http\Exception\InvalidArgumentException',
            'Not valid or not supported HTTP version'
        );
        $request->setVersion('1.2');
    }

    /**
     * @dataProvider getMethods
     */
    public function testRequestMethodCheckWorksForAllMethods($methodName)
    {
        $request = new Request;
        $request->setMethod($methodName);

        foreach ($this->getMethods(false, $methodName) as $testMethodName => $testMethodValue) {
            $this->assertEquals($testMethodValue, $request->{'is' . $testMethodName}());
        }
    }

    public function testRequestCanBeCastToAString()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_GET);
        $request->setUri('/');
        $request->setContent('foo=bar&bar=baz');
        $this->assertEquals("GET / HTTP/1.1\r\n\r\nfoo=bar&bar=baz", $request->toString());
    }

    public function testRequestIsXmlHttpRequest()
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

    public function testRequestIsFlashRequest()
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

    /**
     * @group 4893
     */
    public function testRequestsWithoutHttpVersionAreOK()
    {
        $requestString = "GET http://www.domain.com/index.php";
        $request = Request::fromString($requestString);
        $this->assertEquals($request::METHOD_GET, $request->getMethod());
    }

    /**
     * PHPUNIT DATA PROVIDER
     *
     * @param $providerContext
     * @param null $trueMethod
     * @return array
     */
    public function getMethods($providerContext, $trueMethod = null)
    {
        $refClass = new \ReflectionClass('Laminas\Http\Request');
        $return = array();
        foreach ($refClass->getConstants() as $cName => $cValue) {
            if (substr($cName, 0, 6) == 'METHOD') {
                if ($providerContext) {
                    $return[] = array($cValue);
                } else {
                    $return[strtolower($cValue)] = ($trueMethod == $cValue) ? true : false;
                }
            }
        }
        return $return;
    }

    public function testromStringFactoryCreatesSingleObjectWithHeaderFolding()
    {
        $request = Request::fromString("GET /foo HTTP/1.1\r\nFake: foo\r\n -bar");
        $headers = $request->getHeaders();
        $this->assertEquals(1, $headers->count());

        $header = $headers->get('fake');
        $this->assertInstanceOf('Laminas\Http\Header\GenericHeader', $header);
        $this->assertEquals('Fake', $header->getFieldName());
        $this->assertEquals('foo-bar', $header->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testCRLFAttack()
    {
        $this->setExpectedException('Laminas\Http\Exception\RuntimeException');
        $request = Request::fromString(
            "GET /foo HTTP/1.1\r\nHost: example.com\r\nX-Foo: This\ris\r\n\r\nCRLF\nInjection"
        );
    }
}
