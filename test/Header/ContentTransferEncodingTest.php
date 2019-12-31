<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentTransferEncoding;

class ContentTransferEncodingTest extends \PHPUnit_Framework_TestCase
{
    public function testContentTransferEncodingFromStringCreatesValidContentTransferEncodingHeader()
    {
        $contentTransferEncodingHeader = ContentTransferEncoding::fromString('Content-Transfer-Encoding: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentTransferEncodingHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ContentTransferEncoding', $contentTransferEncodingHeader);
    }

    public function testContentTransferEncodingGetFieldNameReturnsHeaderName()
    {
        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $this->assertEquals('Content-Transfer-Encoding', $contentTransferEncodingHeader->getFieldName());
    }

    public function testContentTransferEncodingGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentTransferEncoding needs to be completed');

        $contentTransferEncodingHeader = new ContentTransferEncoding();
        $this->assertEquals('xxx', $contentTransferEncodingHeader->getFieldValue());
    }

    public function testContentTransferEncodingToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentTransferEncoding needs to be completed');

        $contentTransferEncodingHeader = new ContentTransferEncoding();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Transfer-Encoding: xxx', $contentTransferEncodingHeader->toString());
    }

    /** Implmentation specific tests here */
}
