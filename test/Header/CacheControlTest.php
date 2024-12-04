<?php

declare(strict_types=1);

namespace LaminasTest\Http\Header;

use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\Exception\InvalidArgumentException;
use Laminas\Http\Header\HeaderInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class CacheControlTest extends TestCase
{
    public function testCacheControlFromStringCreatesValidCacheControlHeader(): void
    {
        $cacheControlHeader = CacheControl::fromString('Cache-Control: xxx');
        $this->assertInstanceOf(HeaderInterface::class, $cacheControlHeader);
        $this->assertInstanceOf(CacheControl::class, $cacheControlHeader);
    }

    public function testCacheControlGetFieldNameReturnsHeaderName(): void
    {
        $cacheControlHeader = new CacheControl();
        $this->assertEquals('Cache-Control', $cacheControlHeader->getFieldName());
    }

    public function testCacheControlGetFieldValueReturnsProperValue(): void
    {
        $this->markTestIncomplete('CacheControl needs to be completed');

        $cacheControlHeader = new CacheControl();
        $this->assertEquals('xxx', $cacheControlHeader->getFieldValue());
    }

    public function testCacheControlToStringReturnsHeaderFormattedString(): void
    {
        $this->markTestIncomplete('CacheControl needs to be completed');

        $cacheControlHeader = new CacheControl();

        // @todo set some values, then test output
        $this->assertEmpty('Cache-Control: xxx', $cacheControlHeader->toString());
    }

    // Implementation specific tests here

    // phpcs:ignore Squiz.Commenting.FunctionComment.WrongStyle
    public function testCacheControlIsEmpty(): void
    {
        $cacheControlHeader = new CacheControl();
        $this->assertTrue($cacheControlHeader->isEmpty());
        $cacheControlHeader->addDirective('xxx');
        $this->assertFalse($cacheControlHeader->isEmpty());
        $cacheControlHeader->removeDirective('xxx');
        $this->assertTrue($cacheControlHeader->isEmpty());
    }

    public function testCacheControlAddHasGetRemove(): void
    {
        $cacheControlHeader = new CacheControl();
        $cacheControlHeader->addDirective('xxx');
        $this->assertTrue($cacheControlHeader->hasDirective('xxx'));
        $this->assertTrue($cacheControlHeader->getDirective('xxx'));
        $cacheControlHeader->removeDirective('xxx');
        $this->assertFalse($cacheControlHeader->hasDirective('xxx'));
        $this->assertNull($cacheControlHeader->getDirective('xxx'));

        $cacheControlHeader->addDirective('xxx', 'foo');
        $this->assertTrue($cacheControlHeader->hasDirective('xxx'));
        $this->assertEquals('foo', $cacheControlHeader->getDirective('xxx'));
        $cacheControlHeader->removeDirective('xxx');
        $this->assertFalse($cacheControlHeader->hasDirective('xxx'));
        $this->assertNull($cacheControlHeader->getDirective('xxx'));
    }

    public function testCacheControlGetFieldValue(): void
    {
        $cacheControlHeader = new CacheControl();
        $this->assertEmpty($cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('xxx');
        $this->assertEquals('xxx', $cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('aaa');
        $this->assertEquals('aaa, xxx', $cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('yyy', 'foo');
        $this->assertEquals('aaa, xxx, yyy=foo', $cacheControlHeader->getFieldValue());
        $cacheControlHeader->addDirective('zzz', 'bar, baz');
        $this->assertEquals('aaa, xxx, yyy=foo, zzz="bar, baz"', $cacheControlHeader->getFieldValue());
    }

    public function testCacheControlParse(): void
    {
        $cacheControlHeader = CacheControl::fromString('Cache-Control: a, b=foo, c="bar, baz"');
        $this->assertTrue($cacheControlHeader->hasDirective('a'));
        $this->assertTrue($cacheControlHeader->getDirective('a'));
        $this->assertTrue($cacheControlHeader->hasDirective('b'));
        $this->assertEquals('foo', $cacheControlHeader->getDirective('b'));
        $this->assertTrue($cacheControlHeader->hasDirective('c'));
        $this->assertEquals('bar, baz', $cacheControlHeader->getDirective('c'));
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testPreventsCRLFAttackViaFromString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CacheControl::fromString("Cache-Control: xxx\r\n\r\n");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     */
    #[Group('ZF2015-04')]
    public function testProtectsFromCRLFAttackViaSetters(): void
    {
        $header = new CacheControl();
        $this->expectException(InvalidArgumentException::class);
        $header->addDirective("\rsome\r\ninvalid\nkey", "\ra\r\nCRLF\ninjection");
    }
}
