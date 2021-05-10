<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentEncoding;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class ContentEncodingTest extends TestCase
{
    public function testContentEncodingFromStringCreatesValidContentEncodingHeader()
    {
        $contentEncodingHeader = ContentEncoding::fromString('Content-Encoding: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentEncodingHeader);
        $this->assertInstanceOf(ContentEncoding::class, $contentEncodingHeader);
    }

    public function testContentEncodingGetFieldNameReturnsHeaderName()
    {
        $contentEncodingHeader = new ContentEncoding();
        $this->assertEquals('Content-Encoding', $contentEncodingHeader->getFieldName());
    }

    public function testContentEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentEncoding needs to be completed');

        $contentEncodingHeader = new ContentEncoding();
        $this->assertEquals('xxx', $contentEncodingHeader->getFieldValue());
    }

    public function testContentEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentEncoding needs to be completed');

        $contentEncodingHeader = new ContentEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Encoding: xxx', $contentEncodingHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        ContentEncoding::fromString("Content-Encoding: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentEncoding("xxx\r\n\r\nevilContent");
    }
}
