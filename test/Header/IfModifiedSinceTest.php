<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\IfModifiedSince;
use PHPUnit\Framework\TestCase;

class IfModifiedSinceTest extends TestCase
{
    public function testIfModifiedSinceFromStringCreatesValidIfModifiedSinceHeader()
    {
        $ifModifiedSinceHeader = IfModifiedSince::fromString('If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf(HeaderInterface::class, $ifModifiedSinceHeader);
        $this->assertInstanceOf(IfModifiedSince::class, $ifModifiedSinceHeader);
    }

    public function testIfModifiedSinceGetFieldNameReturnsHeaderName()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $this->assertEquals('If-Modified-Since', $ifModifiedSinceHeader->getFieldName());
    }

    public function testIfModifiedSinceGetFieldValueReturnsProperValue()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $ifModifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $ifModifiedSinceHeader->getFieldValue());
    }

    public function testIfModifiedSinceToStringReturnsHeaderFormattedString()
    {
        $ifModifiedSinceHeader = new IfModifiedSince();
        $ifModifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT', $ifModifiedSinceHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see LaminasTest\Http\Header\DateTest
     */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        IfModifiedSince::fromString(
            "If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT\r\n\r\nevilContent"
        );
    }
}
