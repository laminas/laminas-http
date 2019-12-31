<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\IfMatch;

class IfMatchTest extends \PHPUnit_Framework_TestCase
{

    public function testIfMatchFromStringCreatesValidIfMatchHeader()
    {
        $ifMatchHeader = IfMatch::fromString('If-Match: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $ifMatchHeader);
        $this->assertInstanceOf('Laminas\Http\Header\IfMatch', $ifMatchHeader);
    }

    public function testIfMatchGetFieldNameReturnsHeaderName()
    {
        $ifMatchHeader = new IfMatch();
        $this->assertEquals('If-Match', $ifMatchHeader->getFieldName());
    }

    public function testIfMatchGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();
        $this->assertEquals('xxx', $ifMatchHeader->getFieldValue());
    }

    public function testIfMatchToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-Match: xxx', $ifMatchHeader->toString());
    }

    /** Implmentation specific tests here */

}

