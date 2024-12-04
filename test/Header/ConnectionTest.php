<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Connection;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    public function testConnectionFromStringCreatesValidConnectionHeader(): void
    {
        $connectionHeader = Connection::fromString('Connection: close');
        $this->assertInstanceOf(HeaderInterface::class, $connectionHeader);
        $this->assertInstanceOf(Connection::class, $connectionHeader);
        $this->assertEquals('close', $connectionHeader->getFieldValue());
        $this->assertFalse($connectionHeader->isPersistent());
    }

    public function testConnectionGetFieldNameReturnsHeaderName(): void
    {
        $connectionHeader = new Connection();
        $this->assertEquals('Connection', $connectionHeader->getFieldName());
    }

    public function testConnectionGetFieldValueReturnsProperValue(): void
    {
        $connectionHeader = new Connection();
        $connectionHeader->setValue('Keep-Alive');
        $this->assertEquals('keep-alive', $connectionHeader->getFieldValue());
    }

    public function testConnectionToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('Connection needs to be completed');

        $connectionHeader = new Connection();
        $connectionHeader->setValue('close');
        $this->assertEmpty('Connection: close', $connectionHeader->toString());
    }

    public function testConnectionSetPersistentReturnsProperValue(): void
    {
        $connectionHeader = new Connection();
        $connectionHeader->setPersistent(true);
        $this->assertEquals('keep-alive', $connectionHeader->getFieldValue());
        $connectionHeader->setPersistent(false);
        $this->assertEquals('close', $connectionHeader->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Connection::fromString("Connection: close\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaSetters(): void
    {
        $header = new Connection();
        $this->expectException(InvalidArgumentException::class);
        $header->setValue("close\r\n\r\nevilContent");
    }
}
