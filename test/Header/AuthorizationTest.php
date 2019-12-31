<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Authorization;

class AuthorizationTest extends \PHPUnit_Framework_TestCase
{

    public function testAuthorizationFromStringCreatesValidAuthorizationHeader()
    {
        $authorizationHeader = Authorization::fromString('Authorization: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $authorizationHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Authorization', $authorizationHeader);
    }

    public function testAuthorizationGetFieldNameReturnsHeaderName()
    {
        $authorizationHeader = new Authorization();
        $this->assertEquals('Authorization', $authorizationHeader->getFieldName());
    }

    public function testAuthorizationGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Authorization needs to be completed');

        $authorizationHeader = new Authorization();
        $this->assertEquals('xxx', $authorizationHeader->getFieldValue());
    }

    public function testAuthorizationToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Authorization needs to be completed');

        $authorizationHeader = new Authorization();

        // @todo set some values, then test output
        $this->assertEmpty('Authorization: xxx', $authorizationHeader->toString());
    }

    /** Implmentation specific tests here */

}

