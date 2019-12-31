<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLength;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class ContentLengthTest extends TestCase
{
    public function testContentLengthFromStringCreatesValidContentLengthHeader()
    {
        $contentLengthHeader = ContentLength::fromString('Content-Length: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentLengthHeader);
        $this->assertInstanceOf(ContentLength::class, $contentLengthHeader);
    }

    public function testContentLengthGetFieldNameReturnsHeaderName()
    {
        $contentLengthHeader = new ContentLength();
        $this->assertEquals('Content-Length', $contentLengthHeader->getFieldName());
    }

    public function testContentLengthGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();
        $this->assertEquals('xxx', $contentLengthHeader->getFieldValue());
    }

    public function testContentLengthToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentLength needs to be completed');

        $contentLengthHeader = new ContentLength();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Length: xxx', $contentLengthHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        ContentLength::fromString("Content-Length: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentLength("Content-Length: xxx\r\n\r\nevilContent");
    }

    public function testZeroValue()
    {
        $contentLengthHeader = new ContentLength(0);
        $this->assertEquals(0, $contentLengthHeader->getFieldValue());
        $this->assertEquals('Content-Length: 0', $contentLengthHeader->toString());
    }
}
