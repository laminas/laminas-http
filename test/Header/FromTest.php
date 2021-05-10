<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\From;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class FromTest extends TestCase
{
    public function testFromFromStringCreatesValidFromHeader()
    {
        $fromHeader = From::fromString('From: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $fromHeader);
        $this->assertInstanceOf(From::class, $fromHeader);
    }

    public function testFromGetFieldNameReturnsHeaderName()
    {
        $fromHeader = new From();
        $this->assertEquals('From', $fromHeader->getFieldName());
    }

    public function testFromGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();
        $this->assertEquals('xxx', $fromHeader->getFieldValue());
    }

    public function testFromToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();

        // @todo set some values, then test output
        $this->assertEmpty('From: xxx', $fromHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        From::fromString("From: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new From("xxx\r\n\r\nevilContent");
    }
}
