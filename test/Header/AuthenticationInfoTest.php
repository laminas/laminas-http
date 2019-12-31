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

    /** Implmentation specific tests here */

}

