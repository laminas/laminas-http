<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Range;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    public function testRangeFromStringCreatesValidRangeHeader(): void
    {
        $rangeHeader = Range::fromString('Range: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $rangeHeader);
        $this->assertInstanceOf(Range::class, $rangeHeader);
    }

    public function testRangeGetFieldNameReturnsHeaderName(): void
    {
        $rangeHeader = new Range();
        $this->assertEquals('Range', $rangeHeader->getFieldName());
    }

    public function testRangeGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('Range needs to be completed');

        $rangeHeader = new Range();
        $this->assertEquals('xxx', $rangeHeader->getFieldValue());
    }

    public function testRangeToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('Range needs to be completed');

        $rangeHeader = new Range();

        // @todo set some values, then test output
        $this->assertEmpty('Range: xxx', $rangeHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Range::fromString("Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructorValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Range("xxx\r\n\r\nevilContent");
    }
}
