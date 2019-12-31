<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Etag;

class EtagTest extends \PHPUnit_Framework_TestCase
{

    public function testEtagFromStringCreatesValidEtagHeader()
    {
        $etagHeader = Etag::fromString('Etag: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $etagHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Etag', $etagHeader);
    }

    public function testEtagGetFieldNameReturnsHeaderName()
    {
        $etagHeader = new Etag();
        $this->assertEquals('Etag', $etagHeader->getFieldName());
    }

    public function testEtagGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();
        $this->assertEquals('xxx', $etagHeader->getFieldValue());
    }

    public function testEtagToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Etag needs to be completed');

        $etagHeader = new Etag();

        // @todo set some values, then test output
        $this->assertEmpty('Etag: xxx', $etagHeader->toString());
    }

    /** Implmentation specific tests here */

}

