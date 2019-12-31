<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Trailer;

class TrailerTest extends \PHPUnit_Framework_TestCase
{
    public function testTrailerFromStringCreatesValidTrailerHeader()
    {
        $trailerHeader = Trailer::fromString('Trailer: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $trailerHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Trailer', $trailerHeader);
    }

    public function testTrailerGetFieldNameReturnsHeaderName()
    {
        $trailerHeader = new Trailer();
        $this->assertEquals('Trailer', $trailerHeader->getFieldName());
    }

    public function testTrailerGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Trailer needs to be completed');

        $trailerHeader = new Trailer();
        $this->assertEquals('xxx', $trailerHeader->getFieldValue());
    }

    public function testTrailerToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Trailer needs to be completed');

        $trailerHeader = new Trailer();

        // @todo set some values, then test output
        $this->assertEmpty('Trailer: xxx', $trailerHeader->toString());
    }

    /** Implmentation specific tests here */
}
