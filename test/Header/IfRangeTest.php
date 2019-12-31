<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\IfRange;

class IfRangeTest extends \PHPUnit_Framework_TestCase
{
    public function testIfRangeFromStringCreatesValidIfRangeHeader()
    {
        $ifRangeHeader = IfRange::fromString('If-Range: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $ifRangeHeader);
        $this->assertInstanceOf('Laminas\Http\Header\IfRange', $ifRangeHeader);
    }

    public function testIfRangeGetFieldNameReturnsHeaderName()
    {
        $ifRangeHeader = new IfRange();
        $this->assertEquals('If-Range', $ifRangeHeader->getFieldName());
    }

    public function testIfRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfRange needs to be completed');

        $ifRangeHeader = new IfRange();
        $this->assertEquals('xxx', $ifRangeHeader->getFieldValue());
    }

    public function testIfRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfRange needs to be completed');

        $ifRangeHeader = new IfRange();

        // @todo set some values, then test output
        $this->assertEmpty('If-Range: xxx', $ifRangeHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = IfRange::fromString("If-Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new IfRange("xxx\r\n\r\nevilContent");
    }
}
