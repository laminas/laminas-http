<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Warning;

class WarningTest extends \PHPUnit_Framework_TestCase
{

    public function testWarningFromStringCreatesValidWarningHeader()
    {
        $warningHeader = Warning::fromString('Warning: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $warningHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Warning', $warningHeader);
    }

    public function testWarningGetFieldNameReturnsHeaderName()
    {
        $warningHeader = new Warning();
        $this->assertEquals('Warning', $warningHeader->getFieldName());
    }

    public function testWarningGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Warning needs to be completed');

        $warningHeader = new Warning();
        $this->assertEquals('xxx', $warningHeader->getFieldValue());
    }

    public function testWarningToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Warning needs to be completed');

        $warningHeader = new Warning();

        // @todo set some values, then test output
        $this->assertEmpty('Warning: xxx', $warningHeader->toString());
    }

    /** Implmentation specific tests here */

}
