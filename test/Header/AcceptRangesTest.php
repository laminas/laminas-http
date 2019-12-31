<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptRanges;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class AcceptRangesTest extends TestCase
{
    public function testAcceptRangesFromStringCreatesValidAcceptRangesHeader()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertInstanceOf(HeaderInterface::class, $acceptRangesHeader);
        $this->assertInstanceOf(AcceptRanges::class, $acceptRangesHeader);
    }

    public function testAcceptRangesGetFieldNameReturnsHeaderName()
    {
        $acceptRangesHeader = new AcceptRanges();
        $this->assertEquals('Accept-Ranges', $acceptRangesHeader->getFieldName());
    }

    public function testAcceptRangesGetFieldValueReturnsProperValue()
    {
        $acceptRangesHeader = AcceptRanges::fromString('Accept-Ranges: bytes');
        $this->assertEquals('bytes', $acceptRangesHeader->getFieldValue());
        $this->assertEquals('bytes', $acceptRangesHeader->getRangeUnit());
    }

    public function testAcceptRangesToStringReturnsHeaderFormattedString()
    {
        $acceptRangesHeader = new AcceptRanges();
        $acceptRangesHeader->setRangeUnit('bytes');

        // @todo set some values, then test output
        $this->assertEquals('Accept-Ranges: bytes', $acceptRangesHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        $header = AcceptRanges::fromString("Accept-Ranges: bytes;\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        $header = new AcceptRanges("bytes;\r\n\r\nevilContent");
    }
}
