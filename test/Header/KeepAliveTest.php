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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = KeepAlive::fromString("Keep-Alive: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new KeepAlive("xxx\r\n\r\nevilContent");
    }
}
