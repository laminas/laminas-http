<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\IfModifiedSince;

class IfModifiedSinceTest extends \PHPUnit_Framework_TestCase
{
    public function testIfModifiedSinceFromStringCreatesValidIfModifiedSinceHeader()
    {
        $ifModifiedSinceHeader = IfModifiedSince::fromString('If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $ifModifiedSinceHeader);
        $this->assertInstanceOf('Laminas\Http\Header\IfModifiedSince', $ifModifiedSinceHeader);
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
     * @expectedException Laminas\Http\Header\Exception\InvalidArgumentException
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $header = IfModifiedSince::fromString(
            "If-Modified-Since: Sun, 06 Nov 1994 08:49:37 GMT\r\n\r\nevilContent"
        );
    }
}
