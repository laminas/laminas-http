<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentEncoding;

class ContentEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testContentEncodingFromStringCreatesValidContentEncodingHeader()
    {
        $contentEncodingHeader = ContentEncoding::fromString('Content-Encoding: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentEncodingHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ContentEncoding', $contentEncodingHeader);
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

    /** Implmentation specific tests here */
}
