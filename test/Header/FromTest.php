<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\From;

class FromTest extends \PHPUnit_Framework_TestCase
{

    public function testFromFromStringCreatesValidFromHeader()
    {
        $fromHeader = From::fromString('From: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $fromHeader);
        $this->assertInstanceOf('Laminas\Http\Header\From', $fromHeader);
    }

    public function testFromGetFieldNameReturnsHeaderName()
    {
        $fromHeader = new From();
        $this->assertEquals('From', $fromHeader->getFieldName());
    }

    public function testFromGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();
        $this->assertEquals('xxx', $fromHeader->getFieldValue());
    }

    public function testFromToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('From needs to be completed');

        $fromHeader = new From();

        // @todo set some values, then test output
        $this->assertEmpty('From: xxx', $fromHeader->toString());
    }

    /** Implmentation specific tests here */

}

