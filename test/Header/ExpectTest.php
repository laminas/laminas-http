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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = Expect::fromString("Expect: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new Expect("xxx\r\n\r\nevilContent");
    }
}
