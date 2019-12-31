<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\WWWAuthenticate;

class WWWAuthenticateTest extends \PHPUnit_Framework_TestCase
{

    public function testWWWAuthenticateFromStringCreatesValidWWWAuthenticateHeader()
    {
        $wWWAuthenticateHeader = WWWAuthenticate::fromString('WWW-Authenticate: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $wWWAuthenticateHeader);
        $this->assertInstanceOf('Laminas\Http\Header\WWWAuthenticate', $wWWAuthenticateHeader);
    }

    public function testWWWAuthenticateGetFieldNameReturnsHeaderName()
    {
        $wWWAuthenticateHeader = new WWWAuthenticate();
        $this->assertEquals('WWW-Authenticate', $wWWAuthenticateHeader->getFieldName());
    }

    public function testWWWAuthenticateGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('WWWAuthenticate needs to be completed');

        $wWWAuthenticateHeader = new WWWAuthenticate();
        $this->assertEquals('xxx', $wWWAuthenticateHeader->getFieldValue());
    }

    public function testWWWAuthenticateToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('WWWAuthenticate needs to be completed');

        $wWWAuthenticateHeader = new WWWAuthenticate();

        // @todo set some values, then test output
        $this->assertEmpty('WWW-Authenticate: xxx', $wWWAuthenticateHeader->toString());
    }

    /** Implmentation specific tests here */

}

