<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Vary;
use PHPUnit\Framework\TestCase;

class VaryTest extends TestCase
{
    public function testVaryFromStringCreatesValidVaryHeader()
    {
        $varyHeader = Vary::fromString('Vary: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $varyHeader);
        $this->assertInstanceOf(Vary::class, $varyHeader);
    }

    public function testVaryGetFieldNameReturnsHeaderName()
    {
        $varyHeader = new Vary();
        $this->assertEquals('Vary', $varyHeader->getFieldName());
    }

    public function testVaryGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Vary needs to be completed');

        $varyHeader = new Vary();
        $this->assertEquals('xxx', $varyHeader->getFieldValue());
    }

    public function testVaryToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Vary needs to be completed');

        $varyHeader = new Vary();

        // @todo set some values, then test output
        $this->assertEmpty('Vary: xxx', $varyHeader->toString());
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
        Vary::fromString("Vary: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Vary("xxx\r\n\r\nevilContent");
    }
}
