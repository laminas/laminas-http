<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptCharset;

class AcceptCharsetTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptCharsetFromStringCreatesValidAcceptCharsetHeader()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $acceptCharsetHeader);
        $this->assertInstanceOf('Laminas\Http\Header\AcceptCharset', $acceptCharsetHeader);
    }

    public function testAcceptCharsetGetFieldNameReturnsHeaderName()
    {
        $acceptCharsetHeader = new AcceptCharset();
        $this->assertEquals('Accept-Charset', $acceptCharsetHeader->getFieldName());
    }

    public function testAcceptCharsetGetFieldValueReturnsProperValue()
    {
        $acceptCharsetHeader = AcceptCharset::fromString('Accept-Charset: xxx');
        $this->assertEquals('xxx', $acceptCharsetHeader->getFieldValue());
    }

    public function testAcceptCharsetToStringReturnsHeaderFormattedString()
    {
        $acceptCharsetHeader = new AcceptCharset();
        $acceptCharsetHeader->addCharset('iso-8859-5', 0.8)
                            ->addCharset('unicode-1-1', 1);

        $this->assertEquals('Accept-Charset: iso-8859-5;q=0.8, unicode-1-1', $acceptCharsetHeader->toString());
    }

    /** Implementation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptCharset::fromString('Accept-Charset: iso-8859-5;q=0.8,unicode-1-1');
        $this->assertTrue($header->hasCharset('iso-8859-5'));
        $this->assertTrue($header->hasCharset('unicode-1-1'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptCharset::fromString('Accept-Charset: iso-8859-5;q=0.8,unicode-1-1,*;q=0.4');
        $expected = array(
            'unicode-1-1',
            'iso-8859-5',
            '*'
        );

        $test = array();
        foreach ($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->getCharset());
        }
    }

    public function testWildcharCharset()
    {
        $acceptHeader = new AcceptCharset();
        $acceptHeader->addCharset('iso-8859-5', 0.8)
                     ->addCharset('*', 0.4);

        $this->assertTrue($acceptHeader->hasCharset('iso-8859-5'));
        $this->assertTrue($acceptHeader->hasCharset('unicode-1-1'));
        $this->assertEquals('Accept-Charset: iso-8859-5;q=0.8, *;q=0.4', $acceptHeader->toString());
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException');
        $header = AcceptCharset::fromString("Accept-Charset: iso-8859-5\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaSetters()
    {
        $header = new AcceptCharset();
        $this->setExpectedException('Laminas\Http\Header\Exception\InvalidArgumentException', 'valid type');
        $header->addCharset("\niso\r-8859-\r\n5");
    }
}
