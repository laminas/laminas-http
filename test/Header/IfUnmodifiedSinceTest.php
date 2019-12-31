<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\IfUnmodifiedSince;

class IfUnmodifiedSinceTest extends \PHPUnit_Framework_TestCase
{

    public function testIfUnmodifiedSinceFromStringCreatesValidIfUnmodifiedSinceHeader()
    {
        $ifUnmodifiedSinceHeader = IfUnmodifiedSince::fromString('If-Unmodified-Since: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $ifUnmodifiedSinceHeader);
        $this->assertInstanceOf('Laminas\Http\Header\IfUnmodifiedSince', $ifUnmodifiedSinceHeader);
    }

    public function testIfUnmodifiedSinceGetFieldNameReturnsHeaderName()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $this->assertEquals('If-Unmodified-Since', $ifUnmodifiedSinceHeader->getFieldName());
    }

    public function testIfUnmodifiedSinceGetFieldValueReturnsProperValue()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $ifUnmodifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $ifUnmodifiedSinceHeader->getFieldValue());
    }

    public function testIfUnmodifiedSinceToStringReturnsHeaderFormattedString()
    {
        $ifUnmodifiedSinceHeader = new IfUnmodifiedSince();
        $ifUnmodifiedSinceHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('If-Unmodified-Since: Sun, 06 Nov 1994 08:49:37 GMT', $ifUnmodifiedSinceHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see LaminasTest\Http\Header\DateTest
     */

}

