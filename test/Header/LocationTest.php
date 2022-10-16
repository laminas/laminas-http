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
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @dataProvider locationFromStringCreatesValidLocationHeaderProvider
     * @param string $uri The URL to redirect to
     */
    public function testLocationFromStringCreatesValidLocationHeader($uri)
    {
        $locationHeader = Location::fromString('Location: ' . $uri);
        $this->assertInstanceOf(HeaderInterface::class, $locationHeader);
        $this->assertInstanceOf(Location::class, $locationHeader);
    }

    /** @psalm-return array<array-key, array{0: string}> */
    public function locationFromStringCreatesValidLocationHeaderProvider(): array
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
     *
     * @dataProvider locationCanSetDifferentSchemeUrisProvider
     * @param string $uri
     * @param string $expectedClass
     */
    public function testLocationCanSetDifferentSchemeUris($uri, $expectedClass)
    {
        $locationHeader = new Location();
        $locationHeader->setUri($uri);
        $this->assertInstanceOf($expectedClass, $locationHeader->uri());
    }

    /**
     * Test that we can set a redirect to different URI-schemes via a class
     *
     * @dataProvider locationCanSetDifferentSchemeUrisProvider
     * @param string $uri
     * @param string $expectedClass
     */
    public function testLocationCanSetDifferentSchemeUriObjects($uri, $expectedClass)
    {
        $uri            = UriFactory::factory($uri);
        $locationHeader = new Location();
        $locationHeader->setUri($uri);
        $this->assertInstanceOf($expectedClass, $locationHeader->uri());
    }

    /**
     * Provide data to the locationCanSetDifferentSchemeUris-test
     *
     * @return array
     */
    public function locationCanSetDifferentSchemeUrisProvider()
    {
        return [
            ['http://www.example.com', Http::class],
            ['https://www.example.com', Http::class],
            ['mailto://www.example.com', Mailto::class],
            ['file://www.example.com', File::class],
        ];
    }

    public function testLocationGetFieldValueReturnsProperValue()
    {
        $locationHeader = new Location();
        $locationHeader->setUri('http://www.example.com/');
        $this->assertEquals('http://www.example.com/', $locationHeader->getFieldValue());

        $locationHeader->setUri('/path');
        $this->assertEquals('/path', $locationHeader->getFieldValue());
    }

    public function testLocationToStringReturnsHeaderFormattedString()
    {
        $locationHeader = new Location();
        $locationHeader->setUri('http://www.example.com/path?query');

        $this->assertEquals('Location: http://www.example.com/path?query', $locationHeader->toString());
    }

    // Implementation specific tests here

    // phpcs:ignore Squiz.Commenting.FunctionComment.WrongStyle
    public function testLocationCanSetAndAccessAbsoluteUri()
    {
        $locationHeader = Location::fromString('Location: http://www.example.com/path');
        $uri            = $locationHeader->uri();
        $this->assertInstanceOf(Http::class, $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $locationHeader->getUri());
    }

    public function testLocationCanSetAndAccessRelativeUri()
    {
        $locationHeader = Location::fromString('Location: /path/to');
        $uri            = $locationHeader->uri();
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $locationHeader->getUri());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     *
     * @group ZF2015-04
     */
    public function testCRLFAttack()
    {
        $this->expectException(InvalidArgumentException::class);
        Location::fromString("Location: http://www.example.com/path\r\n\r\nevilContent");
    }
}
