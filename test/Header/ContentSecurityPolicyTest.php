<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Exception\RuntimeException;
use Laminas\Http\Header\ContentSecurityPolicy;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\MultipleHeaderInterface;
use Laminas\Http\Headers;
use PHPUnit\Framework\TestCase;

class ContentSecurityPolicyTest extends TestCase
{
    public function testContentSecurityPolicyFromStringThrowsExceptionIfImproperHeaderNameUsed()
    {
        $this->expectException(InvalidArgumentException::class);
        ContentSecurityPolicy::fromString('X-Content-Security-Policy: default-src *;');
    }

    public function testContentSecurityPolicyFromStringParsesDirectivesCorrectly()
    {
        $csp = ContentSecurityPolicy::fromString(
            "Content-Security-Policy: default-src 'none'; script-src 'self'; img-src 'self'; style-src 'self';"
        );
        $this->assertInstanceOf(MultipleHeaderInterface::class, $csp);
        $this->assertInstanceOf(HeaderInterface::class, $csp);
        $this->assertInstanceOf(ContentSecurityPolicy::class, $csp);
        $directives = [
            'default-src' => "'none'",
            'script-src'  => "'self'",
            'img-src'     => "'self'",
            'style-src'   => "'self'",
        ];
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
        $this->assertInstanceOf(HeaderInterface::class, $csp);
        $this->assertInstanceOf(ContentSecurityPolicy::class, $csp);
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
        $header = 'Content-Security-Policy: default-src https://*.google.com http://foo.com; '
                . 'img-src \'self\'; script-src https://*.googleapis.com https://*.bar.com;';
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
        $this->expectException(InvalidArgumentException::class);
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
        $this->expectException(InvalidArgumentException::class);
        ContentSecurityPolicy::fromString("Content-Security-Policy: default-src 'none'\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaDirective()
    {
        $header = new ContentSecurityPolicy();
        $this->expectException(InvalidArgumentException::class);
        $header->setDirective('default-src', ["\rsome\r\nCRLF\ninjection"]);
    }

    public function testContentSecurityPolicySetDirectiveWithEmptyReportUriDefaultsToUnset()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('report-uri', []);
        $this->assertEquals(
            'Content-Security-Policy: ',
            $csp->toString()
        );
    }

    public function testContentSecurityPolicySetDirectiveWithEmptyReportUriRemovesExistingValue()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('report-uri', ['csp-error']);
        $this->assertEquals(
            'Content-Security-Policy: report-uri csp-error;',
            $csp->toString()
        );

        $csp->setDirective('report-uri', []);
        $this->assertEquals(
            'Content-Security-Policy: ',
            $csp->toString()
        );
    }

    public function testToStringMultipleHeaders()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', ["'self'"]);

        $additional = new ContentSecurityPolicy();
        $additional->setDirective('img-src', ['https://*.github.com']);

        self::assertSame(
            "Content-Security-Policy: default-src 'self';\r\n"
            . "Content-Security-Policy: img-src https://*.github.com;\r\n",
            $csp->toStringMultipleHeaders([$additional])
        );
    }

    public function testToStringMultipleHeadersExceptionIfDifferent()
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective('default-src', ["'self'"]);

        $additional = new GenericHeader();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'The ContentSecurityPolicy multiple header implementation'
            . ' can only accept an array of ContentSecurityPolicy headers'
        );
        $csp->toStringMultipleHeaders([$additional]);
    }

    public function testMultiple()
    {
        $headers = new Headers();
        $headers->addHeader((new ContentSecurityPolicy())->setDirective('default-src', ["'self'"]));
        $headers->addHeader((new ContentSecurityPolicy())->setDirective('img-src', ['https://*.github.com']));

        self::assertSame(
            "Content-Security-Policy: default-src 'self';\r\n"
            . "Content-Security-Policy: img-src https://*.github.com;\r\n",
            $headers->toString()
        );
    }

    public static function validDirectives()
    {
        return [
            ['child-src', ["'self'"],"Content-Security-Policy: child-src 'self';"],
            ['manifest-src', ["'self'"], "Content-Security-Policy: manifest-src 'self';"],
            ['worker-src', ["'self'"], "Content-Security-Policy: worker-src 'self';"],
            ['prefetch-src', ["'self'"], "Content-Security-Policy: prefetch-src 'self';"],
            ['script-src-elem', ["'self'"], "Content-Security-Policy: script-src-elem 'self';"],
            ['script-src-attr', ["'self'"], "Content-Security-Policy: script-src-attr 'self';"],
            ['style-src-elem', ["'self'"], "Content-Security-Policy: style-src-elem 'self';"],
            ['style-src-attr', ["'self'"], "Content-Security-Policy: style-src-attr 'self';"],
            ['base-uri', ["'self'", "'unsafe-inline'"], "Content-Security-Policy: base-uri 'self' 'unsafe-inline';"],
            ['plugin-types', ['text/csv'], 'Content-Security-Policy: plugin-types text/csv;'],
            [
                'form-action',
                ['http://*.example.com', "'self'"],
                "Content-Security-Policy: form-action http://*.example.com 'self';"
            ],
            [
                'frame-ancestors',
                ['http://*.example.com', "'self'"],
                "Content-Security-Policy: frame-ancestors http://*.example.com 'self';"
            ],
            ['navigate-to', ['example.com'], 'Content-Security-Policy: navigate-to example.com;'],
            ['sandbox', ['allow-forms'], 'Content-Security-Policy: sandbox allow-forms;'],

            // Other directives
            ['block-all-mixed-content', [], 'Content-Security-Policy: block-all-mixed-content;'],
            ['require-sri-for', ['script', 'style'], 'Content-Security-Policy: require-sri-for script style;'],
            ['trusted-types', ['*'], 'Content-Security-Policy: trusted-types *;'],
            ['upgrade-insecure-requests', [], 'Content-Security-Policy: upgrade-insecure-requests;'],
        ];
    }

    /**
     * @dataProvider validDirectives
     *
     * @param string $directive
     * @param string[] $values
     * @param string $expected
     */
    public function testContentSecurityPolicySetDirectiveThrowsExceptionIfMissingDirectiveNameGiven(
        $directive,
        array $values,
        $expected
    ) {
        $csp = new ContentSecurityPolicy();
        $csp->setDirective($directive, $values);

        self::assertSame($expected, $csp->toString());
    }

    /**
     * @dataProvider validDirectives
     *
     * @param string $directive
     * @param string[] $values
     * @param string $header
     */
    public function testFromString($directive, array $values, $header)
    {
        $contentSecurityPolicy = ContentSecurityPolicy::fromString($header);

        self::assertArrayHasKey($directive, $contentSecurityPolicy->getDirectives());
        self::assertSame(implode(' ', $values), $contentSecurityPolicy->getDirectives()[$directive]);
    }

    /**
     * @return string
     */
    public function directivesWithoutValue()
    {
        yield ['block-all-mixed-content'];
        yield ['upgrade-insecure-requests'];
    }

    /**
     * @dataProvider directivesWithoutValue
     *
     * @param string $directive
     */
    public function testExceptionWhenProvideValueWithDirectiveWithoutValue($directive)
    {
        $csp = new ContentSecurityPolicy();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($directive);
        $csp->setDirective($directive, ['something']);
    }
}
