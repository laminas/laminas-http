<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\IfMatch;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class IfMatchTest extends TestCase
{
    public function testIfMatchFromStringCreatesValidIfMatchHeader(): void
    {
        $ifMatchHeader = IfMatch::fromString('If-Match: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $ifMatchHeader);
        $this->assertInstanceOf(IfMatch::class, $ifMatchHeader);
    }

    public function testIfMatchGetFieldNameReturnsHeaderName(): void
    {
        $ifMatchHeader = new IfMatch();
        $this->assertEquals('If-Match', $ifMatchHeader->getFieldName());
    }

    public function testIfMatchGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();
        $this->assertEquals('xxx', $ifMatchHeader->getFieldValue());
    }

    public function testIfMatchToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('IfMatch needs to be completed');

        $ifMatchHeader = new IfMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-Match: xxx', $ifMatchHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        IfMatch::fromString("If-Match: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new IfMatch("xxx\r\n\r\nevilContent");
    }
}
