<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\WWWAuthenticate;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class WWWAuthenticateTest extends TestCase
{
    public function testWWWAuthenticateFromStringCreatesValidWWWAuthenticateHeader(): void
    {
        $wWWAuthenticateHeader = WWWAuthenticate::fromString('WWW-Authenticate: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $wWWAuthenticateHeader);
        $this->assertInstanceOf(WWWAuthenticate::class, $wWWAuthenticateHeader);
    }

    public function testWWWAuthenticateGetFieldNameReturnsHeaderName(): void
    {
        $wWWAuthenticateHeader = new WWWAuthenticate();
        $this->assertEquals('WWW-Authenticate', $wWWAuthenticateHeader->getFieldName());
    }

    public function testWWWAuthenticateGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('WWWAuthenticate needs to be completed');

        $wWWAuthenticateHeader = new WWWAuthenticate();
        $this->assertEquals('xxx', $wWWAuthenticateHeader->getFieldValue());
    }

    public function testWWWAuthenticateToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('WWWAuthenticate needs to be completed');

        $wWWAuthenticateHeader = new WWWAuthenticate();

        // @todo set some values, then test output
        $this->assertEmpty('WWW-Authenticate: xxx', $wWWAuthenticateHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        WWWAuthenticate::fromString("WWW-Authenticate: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new WWWAuthenticate("xxx\r\n\r\nevilContent");
    }
}
