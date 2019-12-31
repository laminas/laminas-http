<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentDisposition;

class ContentDispositionTest extends \PHPUnit_Framework_TestCase
{
    public function testContentDispositionFromStringCreatesValidContentDispositionHeader()
    {
        $contentDispositionHeader = ContentDisposition::fromString('Content-Disposition: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentDispositionHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ContentDisposition', $contentDispositionHeader);
    }

    public function testContentDispositionGetFieldNameReturnsHeaderName()
    {
        $contentDispositionHeader = new ContentDisposition();
        $this->assertEquals('Content-Disposition', $contentDispositionHeader->getFieldName());
    }

    public function testContentDispositionGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentDisposition needs to be completed');

        $contentDispositionHeader = new ContentDisposition();
        $this->assertEquals('xxx', $contentDispositionHeader->getFieldValue());
    }

    public function testContentDispositionToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentDisposition needs to be completed');

        $contentDispositionHeader = new ContentDisposition();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Disposition: xxx', $contentDispositionHeader->toString());
    }

    /** Implmentation specific tests here */
}
