<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\KeepAlive;

class KeepAliveTest extends \PHPUnit_Framework_TestCase
{

    public function testKeepAliveFromStringCreatesValidKeepAliveHeader()
    {
        $keepAliveHeader = KeepAlive::fromString('Keep-Alive: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $keepAliveHeader);
        $this->assertInstanceOf('Laminas\Http\Header\KeepAlive', $keepAliveHeader);
    }

    public function testKeepAliveGetFieldNameReturnsHeaderName()
    {
        $keepAliveHeader = new KeepAlive();
        $this->assertEquals('Keep-Alive', $keepAliveHeader->getFieldName());
    }

    public function testKeepAliveGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('KeepAlive needs to be completed');

        $keepAliveHeader = new KeepAlive();
        $this->assertEquals('xxx', $keepAliveHeader->getFieldValue());
    }

    public function testKeepAliveToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('KeepAlive needs to be completed');

        $keepAliveHeader = new KeepAlive();

        // @todo set some values, then test output
        $this->assertEmpty('Keep-Alive: xxx', $keepAliveHeader->toString());
    }

    /** Implmentation specific tests here */

}
