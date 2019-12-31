<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Origin;

class OriginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group 6484
     */
    public function testOriginFieldValueIsAlwaysAString()
    {
        $origin = new Origin();

        $this->assertInternalType('string', $origin->getFieldValue());
    }

    public function testOriginFromStringCreatesValidOriginHeader()
    {
        $OriginHeader = Origin::fromString('Origin: http://laminas.org');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $OriginHeader);
        $this->assertInstanceOf('Laminas\Http\Header\Origin', $OriginHeader);
    }

    public function testOriginGetFieldNameReturnsHeaderName()
    {
        $OriginHeader = new Origin();
        $this->assertEquals('Origin', $OriginHeader->getFieldName());
    }

    public function testOriginGetFieldValueReturnsProperValue()
    {
        $OriginHeader = Origin::fromString('Origin: http://laminas.org');
        $this->assertEquals('http://laminas.org', $OriginHeader->getFieldValue());
    }
}
