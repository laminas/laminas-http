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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = ContentEncoding::fromString("Content-Encoding: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentEncoding("xxx\r\n\r\nevilContent");
    }
}
