<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptRanges;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class AcceptRangesTest extends TestCase
{
    public function testAcceptRangesFromStringCreatesValidAcceptRangesHeader(): void
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertInstanceOf(HeaderInterface::class, $acceptRangesHeader);
        $this->assertInstanceOf(AcceptRanges::class, $acceptRangesHeader);
    }

    public function testAcceptRangesGetFieldNameReturnsHeaderName(): void
    {
        $acceptRangesHeader = new AcceptRanges();
        $this->assertEquals('Accept-Ranges', $acceptRangesHeader->getFieldName());
    }

    public function testAcceptRangesGetFieldValueReturnsProperValue(): void
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertEquals('bytes', $acceptRangesHeader->getFieldValue());
        $this->assertEquals('bytes', $acceptRangesHeader->getRangeUnit());
    }

    public function testAcceptRangesToStringReturnsHeaderFormattedString(): void
    {
        $acceptRangesHeader = new AcceptRanges();
        $acceptRangesHeader->setRangeUnit('bytes');

        // @todo set some values, then test output
        $this->assertEquals('Accept-Ranges: bytes', $acceptRangesHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $header = AcceptRanges::fromString("Accept-Ranges: bytes;\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $header = new AcceptRanges("bytes;\r\n\r\nevilContent");
    }
}
