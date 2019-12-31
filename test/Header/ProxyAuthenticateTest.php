<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ProxyAuthenticate;

class ProxyAuthenticateTest extends \PHPUnit_Framework_TestCase
{
    public function testProxyAuthenticateFromStringCreatesValidProxyAuthenticateHeader()
    {
        $proxyAuthenticateHeader = ProxyAuthenticate::fromString('Proxy-Authenticate: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $proxyAuthenticateHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ProxyAuthenticate', $proxyAuthenticateHeader);
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
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = ProxyAuthenticate::fromString("Proxy-Authenticate: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new ProxyAuthenticate("xxx\r\n\r\nevilContent");
    }
}
