<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Location;
use Laminas\Uri\Http as HttpUri;

class LocationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @paramstring $uri The URL to redirect to
     * @dataProvider locationFromStringCreatesValidLocationHeaderProvider
     */
    public function testLocationFromStringCreatesValidLocationHeader($uri)
    {
        $locationHeader = Location::fromString('Location: ' . $uri);
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $locationHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Location', $locationHeader);
    }

    public function locationFromStringCreatesValidLocationHeaderProvider()
    {
        return array(
            array('http://www.example.com'),
            array('https://www.example.com'),
            array('mailto://www.example.com'),
            array('file://www.example.com'),
        );
    }

    /**
     * Test that we can set a redirect to different URI-Schemes
     *
     * @param string $uri
     * @param string $expectedClass
     *
     * @dataProvider locationCanSetDifferentSchemeUrisProvider
     */
    public function testLocationCanSetDifferentSchemeUris($uri, $expectedClass)
    {
        $locationHeader = new Location;
        $locationHeader->setUri($uri);
        $this->assertAttributeInstanceof($expectedClass, 'uri', $locationHeader);
    }

    /**
     * Test that we can set a redirect to different URI-schemes via a class
     *
     * @param string $uri
     * @param string $expectedClass
     *
     * @dataProvider locationCanSetDifferentSchemeUrisProvider
     */
    public function testLocationCanSetDifferentSchemeUriObjects($uri, $expectedClass)
    {
            $uri = \Laminas\Uri\UriFactory::factory($uri);
        $locationHeader = new Location;
        $locationHeader->setUri($uri);
        $this->assertAttributeInstanceof($expectedClass, 'uri', $locationHeader);

    }

    /**
     * Provide data to the locationCanSetDifferentSchemeUris-test
     *
     * @return array
     */
    public function locationCanSetDifferentSchemeUrisProvider()
    {
        return array(
            array('http://www.example.com', '\Laminas\Uri\Http'),
            array('https://www.example.com', '\Laminas\Uri\Http'),
            array('mailto://www.example.com', '\Laminas\Uri\Mailto'),
            array('file://www.example.com', '\Laminas\Uri\File'),
        );
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

    /** Implementation specific tests  */

    public function testLocationCanSetAndAccessAbsoluteUri()
    {
        $locationHeader = Location::fromString('Location: http://www.example.com/path');
        $uri = $locationHeader->uri();
        $this->assertInstanceOf('Laminas\Uri\Http', $uri);
        $this->assertTrue($uri->isAbsolute());
        $this->assertEquals('http://www.example.com/path', $locationHeader->getUri());
    }

    public function testLocationCanSetAndAccessRelativeUri()
    {
        $locationHeader = Location::fromString('Location: /path/to');
        $uri = $locationHeader->uri();
        $this->assertInstanceOf('Laminas\Uri\Uri', $uri);
        $this->assertFalse($uri->isAbsolute());
        $this->assertEquals('/path/to', $locationHeader->getUri());
    }

}
