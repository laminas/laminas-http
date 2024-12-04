<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Via;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ViaTest extends TestCase
{
    public function testViaFromStringCreatesValidViaHeader(): void
    {
        $viaHeader = Via::fromString('Via: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $viaHeader);
        $this->assertInstanceOf(Via::class, $viaHeader);
    }

    public function testViaGetFieldNameReturnsHeaderName(): void
    {
        $viaHeader = new Via();
        $this->assertEquals('Via', $viaHeader->getFieldName());
    }

    public function testViaGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();
        $this->assertEquals('xxx', $viaHeader->getFieldValue());
    }

    public function testViaToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('Via needs to be completed');

        $viaHeader = new Via();

        // @todo set some values, then test output
        $this->assertEmpty('Via: xxx', $viaHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Via::fromString("Via: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Via("xxx\r\n\r\nevilContent");
    }
}
