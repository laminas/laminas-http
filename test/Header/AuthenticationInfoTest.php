<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AuthenticationInfo;

class AuthenticationInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthenticationInfoFromStringCreatesValidAuthenticationInfoHeader()
    {
        $authenticationInfoHeader = AuthenticationInfo::fromString('Authentication-Info: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $authenticationInfoHeader);
        $this->assertInstanceOf('Laminas\Http\Header\AuthenticationInfo', $authenticationInfoHeader);
    }

    public function testAuthenticationInfoGetFieldNameReturnsHeaderName()
    {
        $authenticationInfoHeader = new AuthenticationInfo();
        $this->assertEquals('Authentication-Info', $authenticationInfoHeader->getFieldName());
    }

    public function testAuthenticationInfoGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('AuthenticationInfo needs to be completed');

        $authenticationInfoHeader = new AuthenticationInfo();
        $this->assertEquals('xxx', $authenticationInfoHeader->getFieldValue());
    }

    public function testAuthenticationInfoToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('AuthenticationInfo needs to be completed');

        $authenticationInfoHeader = new AuthenticationInfo();

        // @todo set some values, then test output
        $this->assertEmpty('Authentication-Info: xxx', $authenticationInfoHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = AuthenticationInfo::fromString("Authentication-Info: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new AuthenticationInfo("xxx\r\n\r\nevilContent");
    }
}
