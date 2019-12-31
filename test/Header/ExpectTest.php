<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Expect;

class ExpectTest extends \PHPUnit_Framework_TestCase
{

    public function testExpectFromStringCreatesValidExpectHeader()
    {
        $expectHeader = Expect::fromString('Expect: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $expectHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Expect', $expectHeader);
    }

    public function testExpectGetFieldNameReturnsHeaderName()
    {
        $expectHeader = new Expect();
        $this->assertEquals('Expect', $expectHeader->getFieldName());
    }

    public function testExpectGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Expect needs to be completed');

        $expectHeader = new Expect();
        $this->assertEquals('xxx', $expectHeader->getFieldValue());
    }

    public function testExpectToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Expect needs to be completed');

        $expectHeader = new Expect();

        // @todo set some values, then test output
        $this->assertEmpty('Expect: xxx', $expectHeader->toString());
    }

    /** Implmentation specific tests here */

}

