<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\UserAgent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase
{
    public function testUserAgentFromStringCreatesValidUserAgentHeader(): void
    {
        $userAgentHeader = UserAgent::fromString('User-Agent: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $userAgentHeader);
        $this->assertInstanceOf(UserAgent::class, $userAgentHeader);
    }

    public function testUserAgentGetFieldNameReturnsHeaderName(): void
    {
        $userAgentHeader = new UserAgent();
        $this->assertEquals('User-Agent', $userAgentHeader->getFieldName());
    }

    public function testUserAgentGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();
        $this->assertEquals('xxx', $userAgentHeader->getFieldValue());
    }

    public function testUserAgentToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();

        // @todo set some values, then test output
        $this->assertEmpty('User-Agent: xxx', $userAgentHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        UserAgent::fromString("User-Agent: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UserAgent("xxx\r\n\r\nevilContent");
    }
}
