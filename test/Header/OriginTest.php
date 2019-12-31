<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Origin;
use Laminas\Uri\Exception\InvalidUriPartException;
use PHPUnit\Framework\TestCase;

class OriginTest extends TestCase
{
    /**
     * @group 6484
     */
    public function testOriginFieldValueIsAlwaysAString()
    {
        $origin = new Origin();

        $this->assertInternalType('string', $origin->getFieldValue());
    }

    public function testOriginFromStringCreatesValidOriginHeader()
    {
        $originHeader = Origin::fromString('Origin: http://laminas.org');
        $this->assertInstanceOf(HeaderInterface::class, $originHeader);
        $this->assertInstanceOf(Origin::class, $originHeader);
    }

    public function testOriginGetFieldNameReturnsHeaderName()
    {
        $originHeader = new Origin();
        $this->assertEquals('Origin', $originHeader->getFieldName());
    }

    public function testOriginGetFieldValueReturnsProperValue()
    {
        $originHeader = Origin::fromString('Origin: http://laminas.org');
        $this->assertEquals('http://laminas.org', $originHeader->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidUriPartException::class);
        Origin::fromString("Origin: http://laminas.org\r\n\r\nevilContent");
    }

    /**
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Origin("http://laminas.org\r\n\r\nevilContent");
    }
}
