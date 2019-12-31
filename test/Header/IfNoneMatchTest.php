<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\IfNoneMatch;

class IfNoneMatchTest extends \PHPUnit_Framework_TestCase
{
    public function testIfNoneMatchFromStringCreatesValidIfNoneMatchHeader()
    {
        $ifNoneMatchHeader = IfNoneMatch::fromString('If-None-Match: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $ifNoneMatchHeader);
        $this->assertInstanceOf('Laminas\Http\Header\IfNoneMatch', $ifNoneMatchHeader);
    }

    public function testIfNoneMatchGetFieldNameReturnsHeaderName()
    {
        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('If-None-Match', $ifNoneMatchHeader->getFieldName());
    }

    public function testIfNoneMatchGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('xxx', $ifNoneMatchHeader->getFieldValue());
    }

    public function testIfNoneMatchToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-None-Match: xxx', $ifNoneMatchHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = IfNoneMatch::fromString("If-None-Match: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new IfNoneMatch("xxx\r\n\r\nevilContent");
    }
}
