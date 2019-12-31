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

    /** Implmentation specific tests here */

}
