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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = ContentDisposition::fromString("Content-Disposition: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentDisposition("xxx\r\n\r\nevilContent");
    }
}
