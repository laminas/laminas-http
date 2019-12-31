<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Expires;

class ExpiresTest extends \PHPUnit_Framework_TestCase
{
    public function testExpiresFromStringCreatesValidExpiresHeader()
    {
        $expiresHeader = Expires::fromString('Expires: Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $expiresHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Expires', $expiresHeader);
    }

    public function testExpiresGetFieldNameReturnsHeaderName()
    {
        $expiresHeader = new Expires();
        $this->assertEquals('Expires', $expiresHeader->getFieldName());
    }

    public function testExpiresGetFieldValueReturnsProperValue()
    {
        $expiresHeader = new Expires();
        $expiresHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Sun, 06 Nov 1994 08:49:37 GMT', $expiresHeader->getFieldValue());
    }

    public function testExpiresToStringReturnsHeaderFormattedString()
    {
        $expiresHeader = new Expires();
        $expiresHeader->setDate('Sun, 06 Nov 1994 08:49:37 GMT');
        $this->assertEquals('Expires: Sun, 06 Nov 1994 08:49:37 GMT', $expiresHeader->toString());
    }

    /**
     * Implementation specific tests are covered by DateTest
     * @see LaminasTest\Http\Header\DateTest
     */

}

