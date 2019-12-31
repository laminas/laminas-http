<?php

/**
 * @see       https://github.com/laminas/laminas-http for the canonical source repository
 * @copyright https://github.com/laminas/laminas-http/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-http/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLanguage;

class ContentLanguageTest extends \PHPUnit_Framework_TestCase
{
    public function testContentLanguageFromStringCreatesValidContentLanguageHeader()
    {
        $contentLanguageHeader = ContentLanguage::fromString('Content-Language: xxx');
        $this->assertInstanceOf('Laminas\Http\Header\HeaderInterface', $contentLanguageHeader);
        $this->assertInstanceOf('Laminas\Http\Header\ContentLanguage', $contentLanguageHeader);
    }

    public function testContentLanguageGetFieldNameReturnsHeaderName()
    {
        $contentLanguageHeader = new ContentLanguage();
        $this->assertEquals('Content-Language', $contentLanguageHeader->getFieldName());
    }

    public function testContentLanguageGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('ContentLanguage needs to be completed');

        $contentLanguageHeader = new ContentLanguage();
        $this->assertEquals('xxx', $contentLanguageHeader->getFieldValue());
    }

    public function testContentLanguageToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('ContentLanguage needs to be completed');

        $contentLanguageHeader = new ContentLanguage();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Language: xxx', $contentLanguageHeader->toString());
    }

    /** Implmentation specific tests here */
}
