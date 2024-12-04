<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Warning;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class WarningTest extends TestCase
{
    public function testWarningFromStringCreatesValidWarningHeader(): void
    {
        $warningHeader = Warning::fromString('Warning: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $warningHeader);
        $this->assertInstanceOf(Warning::class, $warningHeader);
    }

    public function testWarningGetFieldNameReturnsHeaderName(): void
    {
        $warningHeader = new Warning();
        $this->assertEquals('Warning', $warningHeader->getFieldName());
    }

    public function testWarningGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('Warning needs to be completed');

        $warningHeader = new Warning();
        $this->assertEquals('xxx', $warningHeader->getFieldValue());
    }

    public function testWarningToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('Warning needs to be completed');

        $warningHeader = new Warning();

        // @todo set some values, then test output
        $this->assertEmpty('Warning: xxx', $warningHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Warning::fromString("Warning: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Warning("xxx\r\n\r\nevilContent");
    }
}
