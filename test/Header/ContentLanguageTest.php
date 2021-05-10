<?php

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLanguage;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\TestCase;

class ContentLanguageTest extends TestCase
{
    public function testContentLanguageFromStringCreatesValidContentLanguageHeader()
    {
        $contentLanguageHeader = ContentLanguage::fromString('Content-Language: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentLanguageHeader);
        $this->assertInstanceOf(ContentLanguage::class, $contentLanguageHeader);
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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->expectException(InvalidArgumentException::class);
        ContentLanguage::fromString("Content-Language: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentLanguage("xxx\r\n\r\nevilContent");
    }
}
