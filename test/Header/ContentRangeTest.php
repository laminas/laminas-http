<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentRange;

class ContentRangeTest extends \PHPUnit_Framework_TestCase
{
    public function testContentRangeFromStringCreatesValidContentRangeHeader()
    {
        $contentRangeHeader = ContentRange::fromString('Content-Range: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentRangeHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ContentRange', $contentRangeHeader);
    }

    public function testContentRangeGetFieldNameReturnsHeaderName()
    {
        $contentRangeHeader = new ContentRange();
        $this->assertEquals('Content-Range', $contentRangeHeader->getFieldName());
    }

    public function testContentRangeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();
        $this->assertEquals('xxx', $contentRangeHeader->getFieldValue());
    }

    public function testContentRangeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentRange needs to be completed');

        $contentRangeHeader = new ContentRange();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Range: xxx', $contentRangeHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = ContentRange::fromString("Content-Range: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new ContentRange("xxx\r\n\r\nevilContent");
    }
}
