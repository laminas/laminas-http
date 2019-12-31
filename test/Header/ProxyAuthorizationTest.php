<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ProxyAuthorization;

class ProxyAuthorizationTest extends \PHPUnit_Framework_TestCase
{
    public function testProxyAuthorizationFromStringCreatesValidProxyAuthorizationHeader()
    {
        $proxyAuthorizationHeader = ProxyAuthorization::fromString('Proxy-Authorization: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $proxyAuthorizationHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ProxyAuthorization', $proxyAuthorizationHeader);
    }

    public function testProxyAuthorizationGetFieldNameReturnsHeaderName()
    {
        $proxyAuthorizationHeader = new ProxyAuthorization();
        $this->assertEquals('Proxy-Authorization', $proxyAuthorizationHeader->getFieldName());
    }

    public function testProxyAuthorizationGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ProxyAuthorization needs to be completed');

        $proxyAuthorizationHeader = new ProxyAuthorization();
        $this->assertEquals('xxx', $proxyAuthorizationHeader->getFieldValue());
    }

    public function testProxyAuthorizationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ProxyAuthorization needs to be completed');

        $proxyAuthorizationHeader = new ProxyAuthorization();

        // @todo set some values, then test output
        $this->assertEmpty('Proxy-Authorization: xxx', $proxyAuthorizationHeader->toString());
    }

    /** Implmentation specific tests here */
}
