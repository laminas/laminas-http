<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Server;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    public function testServerFromStringCreatesValidServerHeader(): void
    {
        $serverHeader = Server::fromString('Server: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $serverHeader);
        $this->assertInstanceOf(Server::class, $serverHeader);
    }

    public function testServerGetFieldNameReturnsHeaderName(): void
    {
        $serverHeader = new Server();
        $this->assertEquals('Server', $serverHeader->getFieldName());
    }

    public function testServerGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('Server needs to be completed');

        $serverHeader = new Server();
        $this->assertEquals('xxx', $serverHeader->getFieldValue());
    }

    public function testServerToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('Server needs to be completed');

        $serverHeader = new Server();

        // @todo set some values, then test output
        $this->assertEmpty('Server: xxx', $serverHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Server::fromString("Server: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Server("xxx\r\n\r\nevilContent");
    }
}
