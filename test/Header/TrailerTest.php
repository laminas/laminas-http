<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Trailer;
use PHPUnit\Framework\TestCase;

class TrailerTest extends TestCase
{
    public function testTrailerFromStringCreatesValidTrailerHeader()
    {
        $trailerHeader = Trailer::fromString('Trailer: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $trailerHeader);
        $this->assertInstanceOf(Trailer::class, $trailerHeader);
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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        Trailer::fromString("Trailer: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Trailer("xxx\r\n\r\nevilContent");
    }
}
