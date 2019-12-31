<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\MaxForwards;

class MaxForwardsTest extends \PHPUnit_Framework_TestCase
{

    public function testMaxForwardsFromStringCreatesValidMaxForwardsHeader()
    {
        $maxForwardsHeader = MaxForwards::fromString('Max-Forwards: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $maxForwardsHeader);
        $this->assertInstanceOf('Laminas\Http\Header\MaxForwards', $maxForwardsHeader);
    }

    public function testMaxForwardsGetFieldNameReturnsHeaderName()
    {
        $maxForwardsHeader = new MaxForwards();
        $this->assertEquals('Max-Forwards', $maxForwardsHeader->getFieldName());
    }

    public function testMaxForwardsGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('MaxForwards needs to be completed');

        $maxForwardsHeader = new MaxForwards();
        $this->assertEquals('xxx', $maxForwardsHeader->getFieldValue());
    }

    public function testMaxForwardsToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('MaxForwards needs to be completed');

        $maxForwardsHeader = new MaxForwards();

        // @todo set some values, then test output
        $this->assertEmpty('Max-Forwards: xxx', $maxForwardsHeader->toString());
    }

    /** Implmentation specific tests here */

}
