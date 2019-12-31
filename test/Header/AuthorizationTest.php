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
        $authorizationHeader = new Authorization('xxx');
        $this->assertEquals('xxx', $authorizationHeader->getFieldValue());
    }

    public function testAuthorizationToStringReturnsHeaderFormattedString()
    {
        $authorizationHeader = new Authorization('xxx');
        $this->assertEquals('Authorization: xxx', $authorizationHeader->toString());

        $authorizationHeader = Authorization::fromString('Authorization: xxx2');
        $this->assertEquals('Authorization: xxx2', $authorizationHeader->toString());
    }

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = Authorization::fromString("Authorization: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = new Authorization("xxx\r\n\r\nevilContent");
    }
}
