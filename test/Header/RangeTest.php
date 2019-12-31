<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Range;

class RangeTest extends \PHPUnit_Framework_TestCase
{
    public function testRangeFromStringCreatesValidRangeHeader()
    {
        $rangeHeader = Range::fromString('Range: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $rangeHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Range', $rangeHeader);
    }

    public function testRangeGetFieldNameReturnsHeaderName()
    {
        $rangeHeader = new Range();
        $this->assertEquals('Range', $rangeHeader->getFieldName());
    }

    public function testRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Range needs to be completed');

        $rangeHeader = new Range();
        $this->assertEquals('xxx', $rangeHeader->getFieldValue());
    }

    public function testRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Range needs to be completed');

        $rangeHeader = new Range();

        // @todo set some values, then test output
        $this->assertEmpty('Range: xxx', $rangeHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = Range::fromString("Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructorValue()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new Range("xxx\r\n\r\nevilContent");
    }
}
