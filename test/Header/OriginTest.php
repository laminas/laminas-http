<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Origin;
use Laminas\Uri\Exception\InvalidUriPartException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class OriginTest extends TestCase
{
    #[Group('ZF#6484')]
    public function testOriginFieldValueIsAlwaysAString(): void
    {
        $origin = new Origin();

        $this->assertIsString($origin->getFieldValue());
    }

    public function testOriginFromStringCreatesValidOriginHeader(): void
    {
        $originHeader = Origin::fromString('Origin: http://laminas.org');
        $this->assertInstanceOf(HeaderInterface::class, $originHeader);
        $this->assertInstanceOf(Origin::class, $originHeader);
    }

    public function testOriginGetFieldNameReturnsHeaderName(): void
    {
        $originHeader = new Origin();
        $this->assertEquals('Origin', $originHeader->getFieldName());
    }

    public function testOriginGetFieldValueReturnsProperValue(): void
    {
        $originHeader = Origin::fromString('Origin: http://laminas.org');
        $this->assertEquals('http://laminas.org', $originHeader->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidUriPartException::class);
        Origin::fromString("Origin: http://laminas.org\r\n\r\nevilContent");
    }

    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Origin("http://laminas.org\r\n\r\nevilContent");
    }
}
