<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentMD5;

class ContentMD5Test extends \PHPUnit_Framework_TestCase
{
    public function testContentMD5FromStringCreatesValidContentMD5Header()
    {
        $contentMD5Header = ContentMD5::fromString('Content-MD5: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentMD5Header);
        $this->assertInstanceOf('Laminas\Http\Header\ContentMD5', $contentMD5Header);
    }

    public function testContentMD5GetFieldNameReturnsHeaderName()
    {
        $contentMD5Header = new ContentMD5();
        $this->assertEquals('Content-MD5', $contentMD5Header->getFieldName());
    }

    public function testContentMD5GetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentMD5 needs to be completed');

        $contentMD5Header = new ContentMD5();
        $this->assertEquals('xxx', $contentMD5Header->getFieldValue());
    }

    public function testContentMD5ToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentMD5 needs to be completed');

        $contentMD5Header = new ContentMD5();

        // @todo set some values, then test output
        $this->assertEmpty('Content-MD5: xxx', $contentMD5Header->toString());
    }

    /** Implmentation specific tests here */
}
