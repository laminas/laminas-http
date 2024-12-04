<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Header\IfNoneMatch;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class IfNoneMatchTest extends TestCase
{
    public function testIfNoneMatchFromStringCreatesValidIfNoneMatchHeader(): void
    {
        $ifNoneMatchHeader = IfNoneMatch::fromString('If-None-Match: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $ifNoneMatchHeader);
        $this->assertInstanceOf(IfNoneMatch::class, $ifNoneMatchHeader);
    }

    public function testIfNoneMatchGetFieldNameReturnsHeaderName(): void
    {
        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('If-None-Match', $ifNoneMatchHeader->getFieldName());
    }

    public function testIfNoneMatchGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();
        $this->assertEquals('xxx', $ifNoneMatchHeader->getFieldValue());
    }

    public function testIfNoneMatchToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('IfNoneMatch needs to be completed');

        $ifNoneMatchHeader = new IfNoneMatch();

        // @todo set some values, then test output
        $this->assertEmpty('If-None-Match: xxx', $ifNoneMatchHeader->toString());
    }

    /** Implementation specific tests here */
    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        IfNoneMatch::fromString("If-None-Match: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new IfNoneMatch("xxx\r\n\r\nevilContent");
    }
}
