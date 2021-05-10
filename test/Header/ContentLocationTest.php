<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLocation;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Uri\UriInterface;
use PHPUnit\Framework\TestCase;

class ContentLocationTest extends TestCase
{
    public function testContentLocationFromStringCreatesValidLocationHeader()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/');
        $this->assertInstanceOf(HeaderInterface::class, $contentLocationHeader);
        $this->assertInstanceOf(ContentLocation::class, $contentLocationHeader);
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

    /** Implementation specific tests here */

    public function testContentLocationCanSetAndAccessAbsoluteUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: http://www.example.com/path');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $contentLocationHeader->getUri());
    }

    public function testContentLocationCanSetAndAccessRelativeUri()
    {
        $contentLocationHeader = ContentLocation::fromString('Content-Location: /path/to');
        $uri = $contentLocationHeader->uri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $contentLocationHeader->getUri());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        ContentLocation::fromString("Content-Location: /path/to\r\n\r\nevilContent");
    }
}
