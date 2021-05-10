<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\ProxyAuthenticate;
use PHPUnit\Framework\TestCase;

class ProxyAuthenticateTest extends TestCase
{
    public function testProxyAuthenticateFromStringCreatesValidProxyAuthenticateHeader()
    {
        $proxyAuthenticateHeader = ProxyAuthenticate::fromString('Proxy-Authenticate: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $proxyAuthenticateHeader);
        $this->assertInstanceOf(ProxyAuthenticate::class, $proxyAuthenticateHeader);
    }

    public function testProxyAuthenticateGetFieldNameReturnsHeaderName()
    {
        $proxyAuthenticateHeader = new ProxyAuthenticate();
        $this->assertEquals('Proxy-Authenticate', $proxyAuthenticateHeader->getFieldName());
    }

    public function testProxyAuthenticateGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ProxyAuthenticate needs to be completed');

        $proxyAuthenticateHeader = new ProxyAuthenticate();
        $this->assertEquals('xxx', $proxyAuthenticateHeader->getFieldValue());
    }

    public function testProxyAuthenticateToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ProxyAuthenticate needs to be completed');

        $proxyAuthenticateHeader = new ProxyAuthenticate();

        // @todo set some values, then test output
        $this->assertEmpty('Proxy-Authenticate: xxx', $proxyAuthenticateHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        ProxyAuthenticate::fromString("Proxy-Authenticate: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new ProxyAuthenticate("xxx\r\n\r\nevilContent");
    }
}
