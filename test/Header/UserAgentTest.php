<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\UserAgent;
use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase
{
    public function testUserAgentFromStringCreatesValidUserAgentHeader()
    {
        $userAgentHeader = UserAgent::fromString('User-Agent: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $userAgentHeader);
        $this->assertInstanceOf(UserAgent::class, $userAgentHeader);
    }

    public function testUserAgentGetFieldNameReturnsHeaderName()
    {
        $userAgentHeader = new UserAgent();
        $this->assertEquals('User-Agent', $userAgentHeader->getFieldName());
    }

    public function testUserAgentGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();
        $this->assertEquals('xxx', $userAgentHeader->getFieldValue());
    }

    public function testUserAgentToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('UserAgent needs to be completed');

        $userAgentHeader = new UserAgent();

        // @todo set some values, then test output
        $this->assertEmpty('User-Agent: xxx', $userAgentHeader->toString());
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
        UserAgent::fromString("User-Agent: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new UserAgent("xxx\r\n\r\nevilContent");
    }
}
