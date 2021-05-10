<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\IfRange;
use PHPUnit\Framework\TestCase;

class IfRangeTest extends TestCase
{
    public function testIfRangeFromStringCreatesValidIfRangeHeader()
    {
        $ifRangeHeader = IfRange::fromString('If-Range: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $ifRangeHeader);
        $this->assertInstanceOf(IfRange::class, $ifRangeHeader);
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
        $this->expectException(InvalidArgumentException::class);
        IfRange::fromString("If-Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new IfRange("xxx\r\n\r\nevilContent");
    }
}
