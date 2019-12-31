<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\UserAgent;

class UserAgentTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAgentFromStringCreatesValidUserAgentHeader()
    {
        $userAgentHeader = UserAgent::fromString('User-Agent: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $userAgentHeader);
        $this->assertInstanceOf('Laminas\Http\Header\UserAgent', $userAgentHeader);
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
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = UserAgent::fromString("User-Agent: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new UserAgent("xxx\r\n\r\nevilContent");
    }
}
