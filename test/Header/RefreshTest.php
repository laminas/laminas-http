<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Refresh;

class RefreshTest extends \PHPUnit_Framework_TestCase
{

    public function testRefreshFromStringCreatesValidRefreshHeader()
    {
        $refreshHeader = Refresh::fromString('Refresh: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $refreshHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Refresh', $refreshHeader);
    }

    public function testRefreshGetFieldNameReturnsHeaderName()
    {
        $refreshHeader = new Refresh();
        $this->assertEquals('Refresh', $refreshHeader->getFieldName());
    }

    public function testRefreshGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Refresh needs to be completed');

        $refreshHeader = new Refresh();
        $this->assertEquals('xxx', $refreshHeader->getFieldValue());
    }

    public function testRefreshToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Refresh needs to be completed');

        $refreshHeader = new Refresh();

        // @todo set some values, then test output
        $this->assertEmpty('Refresh: xxx', $refreshHeader->toString());
    }

    /** Implmentation specific tests here */

}

