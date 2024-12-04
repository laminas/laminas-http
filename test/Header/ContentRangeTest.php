<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentRange;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ContentRangeTest extends TestCase
{
    public function testContentRangeFromStringCreatesValidContentRangeHeader(): void
    {
        $contentRangeHeader = ContentRange::fromString('Content-Range: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentRangeHeader);
        $this->assertInstanceOf(ContentRange::class, $contentRangeHeader);
    }

    public function testContentRangeGetFieldNameReturnsHeaderName(): void
    {
        $contentRangeHeader = new ContentRange();
        $this->assertEquals('Content-Range', $contentRangeHeader->getFieldName());
    }

    public function testContentRangeGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();
        $this->assertEquals('xxx', $contentRangeHeader->getFieldValue());
    }

    public function testContentRangeToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Range: xxx', $contentRangeHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContentRange::fromString("Content-Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentRange("xxx\r\n\r\nevilContent");
    }
}
