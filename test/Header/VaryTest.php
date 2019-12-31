<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Vary;

class VaryTest extends \PHPUnit_Framework_TestCase
{

    public function testVaryFromStringCreatesValidVaryHeader()
    {
        $varyHeader = Vary::fromString('Vary: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $varyHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Vary', $varyHeader);
    }

    public function testVaryGetFieldNameReturnsHeaderName()
    {
        $varyHeader = new Vary();
        $this->assertEquals('Vary', $varyHeader->getFieldName());
    }

    public function testVaryGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Vary needs to be completed');

        $varyHeader = new Vary();
        $this->assertEquals('xxx', $varyHeader->getFieldValue());
    }

    public function testVaryToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Vary needs to be completed');

        $varyHeader = new Vary();

        // @todo set some values, then test output
        $this->assertEmpty('Vary: xxx', $varyHeader->toString());
    }

    /** Implmentation specific tests here */

}

