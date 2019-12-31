<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Via;

class ViaTest extends \PHPUnit_Framework_TestCase
{
    public function testViaFromStringCreatesValidViaHeader()
    {
        $viaHeader = Via::fromString('Via: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $viaHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Via', $viaHeader);
    }

    public function testViaGetFieldNameReturnsHeaderName()
    {
        $viaHeader = new Via();
        $this->assertEquals('Via', $viaHeader->getFieldName());
    }

    public function testViaGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();
        $this->assertEquals('xxx', $viaHeader->getFieldValue());
    }

    public function testViaToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();

        // @todo set some values, then test output
        $this->assertEmpty('Via: xxx', $viaHeader->toString());
    }

    /** Implmentation specific tests here */
}
