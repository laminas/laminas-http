<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\WWWAuthenticate;
use PHPUnit\Framework\TestCase;

class WWWAuthenticateTest extends TestCase
{
    public function testWWWAuthenticateFromStringCreatesValidWWWAuthenticateHeader()
    {
        $wWWAuthenticateHeader = WWWAuthenticate::fromString('WWW-Authenticate: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $wWWAuthenticateHeader);
        $this->assertInstanceOf(WWWAuthenticate::class, $wWWAuthenticateHeader);
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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        WWWAuthenticate::fromString("WWW-Authenticate: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new WWWAuthenticate("xxx\r\n\r\nevilContent");
    }
}
