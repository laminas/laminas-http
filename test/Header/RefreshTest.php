<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Refresh;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class RefreshTest extends TestCase
{
    public function testRefreshFromStringCreatesValidRefreshHeader(): void
    {
        $refreshHeader = Refresh::fromString('Refresh: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $refreshHeader);
        $this->assertInstanceOf(Refresh::class, $refreshHeader);
    }

    public function testRefreshGetFieldNameReturnsHeaderName(): void
    {
        $refreshHeader = new Refresh();
        $this->assertEquals('Refresh', $refreshHeader->getFieldName());
    }

    public function testRefreshGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('Refresh needs to be completed');

        $refreshHeader = new Refresh();
        $this->assertEquals('xxx', $refreshHeader->getFieldValue());
    }

    public function testRefreshToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('Refresh needs to be completed');

        $refreshHeader = new Refresh();

        // @todo set some values, then test output
        $this->assertEmpty('Refresh: xxx', $refreshHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Refresh::fromString("Refresh: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructorValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Refresh("xxx\r\n\r\nevilContent");
    }
}
