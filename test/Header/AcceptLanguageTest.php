<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\AcceptLanguage;

class AcceptLanguageTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptLanguageFromStringCreatesValidAcceptLanguageHeader()
    {
        $acceptLanguageHeader = AcceptLanguage::fromString('Accept-Language: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $acceptLanguageHeader);
        $this->assertInstanceOf('Laminas\Http\Header\AcceptLanguage', $acceptLanguageHeader);
    }

    public function testAcceptLanguageGetFieldNameReturnsHeaderName()
    {
        $acceptLanguageHeader = new AcceptLanguage();
        $this->assertEquals('Accept-Language', $acceptLanguageHeader->getFieldName());
    }

    public function testAcceptLanguageGetFieldValueReturnsProperValue()
    {
        $acceptLanguageHeader = AcceptLanguage::fromString('Accept-Language: xxx');
        $this->assertEquals('xxx', $acceptLanguageHeader->getFieldValue());
    }

    public function testAcceptLanguageToStringReturnsHeaderFormattedString()
    {
        $acceptLanguageHeader = new AcceptLanguage();
        $acceptLanguageHeader->addLanguage('da', 0.8)
                             ->addLanguage('en-gb', 1);

        $this->assertEquals('Accept-Language: da;q=0.8, en-gb', $acceptLanguageHeader->toString());
    }

    /** Implmentation specific tests here */

    public function testCanParseCommaSeparatedValues()
    {
        $header = AcceptLanguage::fromString('Accept-Language: da;q=0.8, en-gb');
        $this->assertTrue($header->hasLanguage('da'));
        $this->assertTrue($header->hasLanguage('en-gb'));
    }

    public function testPrioritizesValuesBasedOnQParameter()
    {
        $header   = AcceptLanguage::fromString('Accept-Language: da;q=0.8, en-gb, *;q=0.4');
        $expected = array(
            'en-gb',
            'da',
            '*'
        );

        $test = array();
        foreach ($header->getPrioritized() as $type) {
            $this->assertEquals(array_shift($expected), $type->typeString);
        }
        $this->assertEquals($expected, $test);
    }

    public function testWildcharLanguage()
    {
        $acceptHeader = new AcceptLanguage();
        $acceptHeader->addLanguage('da', 0.8)
                     ->addLanguage('*', 0.4);

        $this->assertTrue($acceptHeader->hasLanguage('da'));
        $this->assertTrue($acceptHeader->hasLanguage('en'));
        $this->assertEquals('Accept-Language: da;q=0.8, *;q=0.4', $acceptHeader->toString());
    }

    public function testWildcards()
    {
        $accept = AcceptLanguage::fromString('*, en-*, en-us');
        $res = $accept->getPrioritized();

        $this->assertEquals('en-us', $res[0]->getLanguage());
        $this->assertEquals('en', $res[0]->getPrimaryTag());
        $this->assertEquals('us', $res[0]->getSubTag());

        $this->assertEquals('en-*', $res[1]->getLanguage());
        $this->assertEquals('en', $res[1]->getPrimaryTag());

        $this->assertTrue($accept->hasLanguage('nl'));
    }
}
