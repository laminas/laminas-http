<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLength;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ContentLengthTest extends TestCase
{
    public function testContentLengthFromStringCreatesValidContentLengthHeader(): void
    {
        $contentLengthHeader = ContentLength::fromString('Content-Length: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentLengthHeader);
        $this->assertInstanceOf(ContentLength::class, $contentLengthHeader);
    }

    public function testContentLengthGetFieldNameReturnsHeaderName(): void
    {
        $contentLengthHeader = new ContentLength();
        $this->assertEquals('Content-Length', $contentLengthHeader->getFieldName());
    }

    public function testContentLengthGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();
        $this->assertEquals('xxx', $contentLengthHeader->getFieldValue());
    }

    public function testContentLengthToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Length: xxx', $contentLengthHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContentLength::fromString("Content-Length: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentLength("Content-Length: xxx\r\n\r\nevilContent");
    }

    public function testZeroValue(): void
    {
        $contentLengthHeader = new ContentLength(0);
        $this->assertEquals(0, $contentLengthHeader->getFieldValue());
        $this->assertEquals('Content-Length: 0', $contentLengthHeader->toString());
    }
}
