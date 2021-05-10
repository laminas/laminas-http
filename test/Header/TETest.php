<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\TE;
use PHPUnit\Framework\TestCase;

class TETest extends TestCase
{
    public function testTEFromStringCreatesValidTEHeader()
    {
        $tEHeader = TE::fromString('TE: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $tEHeader);
        $this->assertInstanceOf(TE::class, $tEHeader);
    }

    public function testTEGetFieldNameReturnsHeaderName()
    {
        $tEHeader = new TE();
        $this->assertEquals('TE', $tEHeader->getFieldName());
    }

    public function testTEGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();
        $this->assertEquals('xxx', $tEHeader->getFieldValue());
    }

    public function testTEToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('TE needs to be completed');

        $tEHeader = new TE();

        // @todo set some values, then test output
        $this->assertEmpty('TE: xxx', $tEHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        TE::fromString("TE: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new TE("xxx\r\n\r\nevilContent");
    }
}
