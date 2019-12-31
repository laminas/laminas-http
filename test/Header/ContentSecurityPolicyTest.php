<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentSecurityPolicy;

class ContentSecurityPolicyTest extends \PHPUnit_Framework_TestCase
{
    public function testContentSecurityPolicyFromStringThrowsExceptionIfImproperHeaderNameUsed()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        ContentSecurityPolicy::fromString('X-Content-Security-Policy: default-src *;');
    }

    public function testContentSecurityPolicyFromStringParsesDirectivesCorrectly()
    {
        $csp = ContentSecurityPolicy::fromString(
            "Content-Security-Policy: default-src 'none'; script-src 'self'; img-src 'self'; style-src 'self';"
        );
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $csp);
        $this->assertInstanceOf('Laminas\Http\Header\ContentSecurityPolicy', $csp);
        $directives = ['default-src' => "'none'",
                            'script-src'  => "'self'",
                            'img-src'     => "'self'",
                            'style-src'   => "'self'"];
        $this->assertEquals($directives, $csp->getDirectives());
    }

    public function testContentSecurityPolicyGetFieldNameReturnsHeaderName()
    {
        $csp = new ContentSecurityPolicy();
        $this->assertEquals('Content-Security-Policy', $csp->getFieldName());
    }

    public function testContentSecurityPolicyToStringReturnsHeaderFormattedString()
    {
        $csp = ContentSecurityPolicy::fromString(
            "Content-Security-Policy: default-src 'none'; img-src 'self' https://*.gravatar.com;"
        );
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $csp);
        $this->assertInstanceOf('Laminas\Http\Header\ContentSecurityPolicy', $csp);
        $this->assertEquals(
            "Content-Security-Policy: default-src 'none'; img-src 'self' https://*.gravatar.com;",
            $csp->toString()
        );
    }

    public function testContentSecurityPolicySetDirective()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', ['https://*.google.com', 'http://foo.com'])
            ->setDirective('img-src', ["'self'"])
            ->setDirective('script-src', ['https://*.googleapis.com', 'https://*.bar.com']);
        $header = "Content-Security-Policy: default-src https://*.google.com http://foo.com; "
                . "img-src 'self'; script-src https://*.googleapis.com https://*.bar.com;";
        $this->assertEquals($header, $csp->toString());
    }

    public function testContentSecurityPolicySetDirectiveWithEmptySourcesDefaultsToNone()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', ["'self'"])
            ->setDirective('img-src', ['*'])
            ->setDirective('script-src', []);
        $this->assertEquals(
            "Content-Security-Policy: default-src 'self'; img-src *; script-src 'none';",
            $csp->toString()
        );
    }

    public function testContentSecurityPolicySetDirectiveThrowsExceptionIfInvalidDirectiveNameGiven()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('foo', []);
    }

    public function testContentSecurityPolicyGetFieldValueReturnsProperValue()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', ["'self'"])
            ->setDirective('img-src', ['https://*.github.com']);
        $this->assertEquals("default-src 'self'; img-src https://*.github.com;", $csp->getFieldValue());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = ContentSecurityPolicy::fromString("Content-Security-Policy: default-src 'none'\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaDirective()
    {
        $header = new ContentSecurityPolicy();
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header->setDirective('default-src', ["\rsome\r\nCRLF\ninjection"]);
    }
}
