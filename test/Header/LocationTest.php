<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\Location;
use Laminas\Uri\File;
use Laminas\Uri\Http;
use Laminas\Uri\Mailto;
use Laminas\Uri\Uri;
use Laminas\Uri\UriFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @param string $uri The URL to redirect to
     */
    #[DataProvider('locationFromStringCreatesValidLocationHeaderProvider')]
    public function testLocationFromStringCreatesValidLocationHeader(string $uri): void
    {
        $locationHeader = Location::fromString('Location: ' . $uri);
        $this->assertInstanceOf(HeaderInterface::class, $locationHeader);
        $this->assertInstanceOf(Location::class, $locationHeader);
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public static function locationFromStringCreatesValidLocationHeaderProvider(): array
    {
        return [
            ['http://www.example.com'],
            ['https://www.example.com'],
            ['mailto://www.example.com'],
            ['file://www.example.com'],
        ];
    }

    /**
     * Test that we can set a redirect to different URI-Schemes
     */
    #[DataProvider('locationCanSetDifferentSchemeUrisProvider')]
    public function testLocationCanSetDifferentSchemeUris(string $uri, string $expectedClass): void
    {
        $locationHeader = new Location();
        $locationHeader->setUri($uri);
        $this->assertInstanceOf($expectedClass, $locationHeader->uri());
    }

    /**
     * Test that we can set a redirect to different URI-schemes via a class
     */
    #[DataProvider('locationCanSetDifferentSchemeUrisProvider')]
    public function testLocationCanSetDifferentSchemeUriObjects(string $uri, string $expectedClass): void
    {
        $uri            = UriFactory::factory($uri);
        $locationHeader = new Location();
        $locationHeader->setUri($uri);
        $this->assertInstanceOf($expectedClass, $locationHeader->uri());
    }

    /**
     * Provide data to the locationCanSetDifferentSchemeUris-test
     */
    public static function locationCanSetDifferentSchemeUrisProvider(): array
    {
        return [
            ['http://www.example.com', Http::class],
            ['https://www.example.com', Http::class],
            ['mailto://www.example.com', Mailto::class],
            ['file://www.example.com', File::class],
        ];
    }

    public function testLocationGetFieldValueReturnsProperValue(): void
    {
        $locationHeader = new Location();
        $locationHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $locationHeader->getFieldValue());

        $locationHeader->setUri('/path');
        $this->assertEquals('/path', $locationHeader->getFieldValue());
    }

    public function testLocationToStringReturnsHeaderFormattedString(): void
    {
        $locationHeader = new Location();
        $locationHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Location: http://www.example.com/path?query', $locationHeader->toString());
    }

    // Implementation specific tests here

    // phpcs:ignore Squiz.Commenting.FunctionComment.WrongStyle
    public function testLocationCanSetAndAccessAbsoluteUri(): void
    {
        $locationHeader = Location::fromString('Location: http://www.example.com/path');
        $uri            = $locationHeader->uri();
        $this->assertInstanceOf(Http::class, $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $locationHeader->getUri());
    }

    public function testLocationCanSetAndAccessRelativeUri(): void
    {
        $locationHeader = Location::fromString('Location: /path/to');
        $uri            = $locationHeader->uri();
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $locationHeader->getUri());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testCRLFAttack(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Location::fromString("Location: http://www.example.com/path\r\n\r\nevilContent");
    }
}
