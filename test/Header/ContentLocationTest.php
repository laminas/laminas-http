<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLocation;

class ContentLocationTest extends \PHPUnit_Framework_TestCase
{
    public function testContentLocationFromStringCreatesValidLocationHeader()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentLocationHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ContentLocation', $contentLocationHeader);
    }

    public function testContentLocationGetFieldValueReturnsProperValue()
    {
        $contentLocationHeader = new ContentLocation();
        $contentLocationHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $contentLocationHeader->getFieldValue());

        $contentLocationHeader->setUri('/path');
        $this->assertEquals('/path', $contentLocationHeader->getFieldValue());
    }

    public function testContentLocationToStringReturnsHeaderFormattedString()
    {
        $contentLocationHeader = new ContentLocation();
        $contentLocationHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Content-Location: http://www.example.com/path?query', $contentLocationHeader->toString());
    }

    /** Implementation specific tests  */

    public function testContentLocationCanSetAndAccessAbsoluteUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/path');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf('Laminas\Uri\UriInterface', $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $contentLocationHeader->getUri());
    }

    public function testContentLocationCanSetAndAccessRelativeUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: /path/to');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf('Laminas\Uri\UriInterface', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $contentLocationHeader->getUri());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = ContentLocation::fromString("Content-Location: /path/to\r\n\r\nevilContent");
    }
}
