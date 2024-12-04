<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\ContentLanguage;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class ContentLanguageTest extends TestCase
{
    public function testContentLanguageFromStringCreatesValidContentLanguageHeader(): void
    {
        $contentLanguageHeader = ContentLanguage::fromString('Content-Language: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $contentLanguageHeader);
        $this->assertInstanceOf(ContentLanguage::class, $contentLanguageHeader);
    }

    public function testContentLanguageGetFieldNameReturnsHeaderName(): void
    {
        $contentLanguageHeader = new ContentLanguage();
        $this->assertEquals('Content-Language', $contentLanguageHeader->getFieldName());
    }

    public function testContentLanguageGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('ContentLanguage needs to be completed');

        $contentLanguageHeader = new ContentLanguage();
        $this->assertEquals('xxx', $contentLanguageHeader->getFieldValue());
    }

    public function testContentLanguageToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('ContentLanguage needs to be completed');

        $contentLanguageHeader = new ContentLanguage();

        // @todo set some values, then test output
        $this->assertEmpty('Content-Language: xxx', $contentLanguageHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContentLanguage::fromString("Content-Language: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ContentLanguage("xxx\r\n\r\nevilContent");
    }
}
