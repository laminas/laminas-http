<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentType;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function implode;
use function strtolower;

class ContentTypeTest extends TestCase
{
    public function testContentTypeFromStringCreatesValidContentTypeHeader(): void
    {
        $contentTypeHeader = ContentType::fromString('Content-Type: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentTypeHeader);
        $this->assertInstanceOf(ContentType::class, $contentTypeHeader);
    }

    public function testContentTypeGetFieldNameReturnsHeaderName(): void
    {
        $contentTypeHeader = new ContentType();
        $this->assertEquals('Content-Type', $contentTypeHeader->getFieldName());
    }

    public function testContentTypeGetFieldValueReturnsProperValue(): void
    {
        $header = ContentType::fromString('Content-Type: application/json');
        $this->assertEquals('application/json', $header->getFieldValue());
    }

    public function testContentTypeToStringReturnsHeaderFormattedString(): void
    {
        $header = new ContentType();
        $header->setMediaType('application/atom+xml')
               ->setCharset('ISO-8859-1');

        $this->assertEquals('Content-Type: application/atom+xml; charset=ISO-8859-1', $header->toString());
    }

    // Implementation specific tests here

    /** @psalm-return array<string, array{0: string}> */
    public static function wildcardMatches(): array
    {
        return [
            'wildcard'                                            => ['*/*'],
            'wildcard-format'                                     => ['*/*+*'],
            'wildcard-type-subtype-fixed-format'                  => ['*/*+json'],
            'wildcard-type-partial-wildcard-subtype-fixed-format' => ['*/vnd.*+json'],
            'wildcard-type-format-subtype'                        => ['*/json'],
            'fixed-type-wildcard-subtype'                         => ['application/*'],
            'fixed-type-wildcard-subtype-fixed-format'            => ['application/*+json'],
            'fixed-type-format-subtype'                           => ['application/json'],
            'fixed-type-fixed-subtype-wildcard-format'            => ['application/vnd.foobar+*'],
            'fixed-type-partial-wildcard-subtype-fixed-format'    => ['application/vnd.*+json'],
            'fixed'                                               => ['application/vnd.foobar+json'],
            'fixed-mixed-case'                                    => ['APPLICATION/vnd.FooBar+json'],
        ];
    }

    #[DataProvider('wildcardMatches')]
    public function testMatchWildCard(string $matchAgainst): void
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($matchAgainst);
        $this->assertEquals(strtolower($matchAgainst), $result);
    }

    /** @psalm-return array<string, array{0: string}> */
    public static function invalidMatches(): array
    {
        return [
            'format'                         => ['application/vnd.foobar+xml'],
            'wildcard-subtype'               => ['application/vendor.*+json'],
            'subtype'                        => ['application/vendor.foobar+json'],
            'type'                           => ['text/vnd.foobar+json'],
            'wildcard-type-format'           => ['*/vnd.foobar+xml'],
            'wildcard-type-wildcard-subtype' => ['*/vendor.*+json'],
            'wildcard-type-subtype'          => ['*/vendor.foobar+json'],
        ];
    }

    #[DataProvider('invalidMatches')]
    public function testFailedMatches(string $matchAgainst): void
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($matchAgainst);
        $this->assertFalse($result);
    }

    /** @psalm-return array<string, array{0: string|string[]}> */
    public static function multipleCriteria(): array
    {
        $criteria = [
            'application/vnd.foobar+xml',
            'application/vnd.*+json',
            'application/vendor.foobar+xml',
            '*/vnd.foobar+json',
        ];
        return [
            'array'  => [$criteria],
            'string' => [implode(',', $criteria)],
        ];
    }

    #[DataProvider('multipleCriteria')]
    public function testReturnsMatchingMediaTypeOfFirstCriteriaToValidate(array|string $criteria): void
    {
        $header = ContentType::fromString('Content-Type: application/vnd.foobar+json');
        $result = $header->match($criteria);
        $this->assertEquals('application/vnd.*+json', $result);
    }

    /** @psalm-return array<string, array{0: string, 1: string}> */
    public static function contentTypeParameterExamples(): array
    {
        return [
            'no-quotes'              => ['Content-Type: foo/bar; param=baz', 'baz'],
            'with-quotes'            => ['Content-Type: foo/bar; param="baz"', 'baz'],
            'with-equals'            => ['Content-Type: foo/bar; param=baz=bat', 'baz=bat'],
            'with-equals-and-quotes' => ['Content-Type: foo/bar; param="baz=bat"', 'baz=bat'],
        ];
    }

    #[DataProvider('contentTypeParameterExamples')]
    public function testContentTypeParsesParametersCorrectly(string $headerString, string $expectedParameterValue): void
    {
        $contentTypeHeader = ContentType::fromString($headerString);

        $parameters = $contentTypeHeader->getParameters();

        $this->assertArrayHasKey('param', $parameters);
        $this->assertSame($expectedParameterValue, $parameters['param']);
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContentType::fromString("Content-Type: foo/bar;\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentType("foo/bar\r\n\r\nevilContent");
    }
}
